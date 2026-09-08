#!/usr/bin/env bash
# ============================================================
# CAMPUSHUB — Start Localhost (MariaDB + PHP Server)
# Created by Risky Manuel Tamba
#
# Cara pakai:
#   ./start-server.sh          -> jalankan server
#   ./start-server.sh stop     -> matikan server
#   ./start-server.sh status   -> cek status
# ============================================================
set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MYSQL_SOCKET="$HOME/mysql-data/mysqld.sock"
MYSQL_PID="$HOME/mysql-data/mysqld.pid"
MYSQL_LOG="$HOME/mysql-data/mysqld.log"
PHP_LOG="$HOME/mysql-data/php-server.log"
MARIADB_BIN="/usr/bin/mariadbd"
MARIADB_INSTALL="/usr/bin/mariadb-install-db"
MYSQL_HOST="127.0.0.1"
MYSQL_PORT="3306"
PHP_PORT="${PHP_PORT:-8000}"
PHP_HOST="${PHP_HOST:-127.0.0.1}"

is_mysql_running() {
    [ -S "$MYSQL_SOCKET" ] && pgrep -x mariadbd >/dev/null 2>&1
}

is_php_running() {
    curl -s -o /dev/null -w '%{http_code}' "http://$PHP_HOST:$PHP_PORT/login.php" 2>/dev/null | grep -q 200
}

start() {
    echo "=== CAMPUSHUB — Local Server ==="

    # 1) MariaDB
    if is_mysql_running; then
        echo "  [OK] MariaDB sudah jalan (socket: $MYSQL_SOCKET)"
    else
        echo "  [..] Menjalankan MariaDB..."
        mkdir -p "$HOME/mysql-data"
        if [ ! -d "$HOME/mysql-data/mysql" ]; then
            echo "  [..] Inisialisasi data dir pertama kali..."
            "$MARIADB_INSTALL" \
                --datadir="$HOME/mysql-data" --user="$(whoami)" >/dev/null 2>&1 || true
        fi
        nohup "$MARIADB_BIN" \
            --datadir="$HOME/mysql-data" \
            --user="$(whoami)" \
            --port="$MYSQL_PORT" \
            --bind-address="$MYSQL_HOST" \
            --socket="$MYSQL_SOCKET" \
            --pid-file="$MYSQL_PID" \
            --skip-grant-tables \
            --log-error="$MYSQL_LOG" \
            > "$HOME/mysql-data/server.out" 2>&1 &
        for i in $(seq 1 30); do
            sleep 1
            is_mysql_running && break
        done
        if ! is_mysql_running; then
            echo "  [ERROR] MariaDB gagal start. Cek log: $MYSQL_LOG"
            exit 1
        fi
        echo "  [OK] MariaDB jalan (port $MYSQL_PORT, socket: $MYSQL_SOCKET)"
    fi

    # 2) Import database schema
    php -d extension=pdo_mysql -d pdo_mysql.default_socket="$MYSQL_SOCKET" -r '
        $sock = $argv[1];
        try {
            $p = new PDO("mysql:unix_socket=$sock;charset=utf8mb4", "root", "");
            $p->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // best-effort: set root tanpa password (gagal diabaikan saat --skip-grant-tables)
            try { $p->exec("ALTER USER \"root\"@\"localhost\" IDENTIFIED VIA mysql_native_password USING PASSWORD(\"\")"); } catch (Exception $e) {}
            $p->exec("CREATE DATABASE IF NOT EXISTS campushub");
            $has = $p->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=\"campushub\" AND table_name=\"users\"")->fetchColumn();
            if (!$has) {
                $sql = file_get_contents($argv[2]);
                $p->exec($sql);
                echo "  [OK] Database campushub diimport.\n";
            } else {
                echo "  [OK] Database campushub sudah ada.\n";
            }
        } catch (Exception $e) {
            echo "  [WARN] DB skip: " . $e->getMessage() . "\n";
        }
    ' "$MYSQL_SOCKET" "$PROJECT_DIR/database/schema.sql"

    # 3) PHP server
    if is_php_running; then
        echo "  [OK] PHP server sudah jalan: http://$PHP_HOST:$PHP_PORT/"
    else
        echo "  [..] Menjalankan PHP server..."
        nohup php \
            -d extension=pdo_mysql \
            -d pdo_mysql.default_socket="$MYSQL_SOCKET" \
            -d mysqli.default_socket="$MYSQL_SOCKET" \
            -S "$PHP_HOST:$PHP_PORT" \
            -t "$PROJECT_DIR" \
            > "$PHP_LOG" 2>&1 &
        for i in $(seq 1 15); do
            sleep 1
            is_php_running && break
        done
        if ! is_php_running; then
            echo "  [ERROR] PHP server gagal start. Cek log: $PHP_LOG"
            exit 1
        fi
        echo "  [OK] PHP server jalan"
    fi

    echo ""
    echo "  Campushub siap! Buka di browser:"
    echo ""
    echo "      http://$PHP_HOST:$PHP_PORT/"
    echo ""
    echo "  Akun demo:"
    echo "      Admin: admin@campushub.id  (password: admin123)"
    echo "      Atau daftar akun baru di /register.php"
    echo ""
    echo "  Untuk mematikan:  ./start-server.sh stop"
}

stop() {
    echo "=== Mematikan server ==="
    if is_php_running || [ -f "$PHP_LOG" ]; then
        pkill -f "php.*-S $PHP_HOST:$PHP_PORT" 2>/dev/null || true
        echo "  [OK] PHP server dimatikan"
    fi
    if is_mysql_running; then
        kill "$(cat "$MYSQL_PID" 2>/dev/null)" 2>/dev/null || pkill -x mariadbd 2>/dev/null || true
        sleep 2
        echo "  [OK] MariaDB dimatikan"
    else
        echo "  [..] MariaDB tidak berjalan"
    fi
}

status() {
    echo "=== Status server ==="
    if is_mysql_running; then echo "  MariaDB    : RUNNING ($MYSQL_SOCKET)"; else echo "  MariaDB    : stopped"; fi
    if is_php_running; then echo "  PHP server : RUNNING (http://$PHP_HOST:$PHP_PORT/)"; else echo "  PHP server : stopped"; fi
}

case "${1:-start}" in
    start)  start ;;
    stop)   stop ;;
    status) status ;;
    *) echo "Usage: $0 {start|stop|status}" >&2; exit 1 ;;
esac
