<?php
/**
 * CampusHub - Theme Light/Dark Mode
 * Menggunakan localStorage untuk menyimpan preferensi tema
 * Auto-detect based on system preference
 */
if (!defined('IN_CAMPUSHUB')) die('Akses dilarang');

// Get current theme from localStorage or system preference
$stored_theme = isset($_COOKIE['campushub_theme']) ? $_COOKIE['campushub_theme'] : '';
$system_prefers_dark = strpos($_SERVER['HTTP_USER_AGENT'] ?? '', 'Chrome') !== false; // Simplified check

// Determine current theme
if ($stored_theme === 'dark') {
    $current_theme = 'dark';
} elseif ($stored_theme === 'light') {
    $current_theme = 'light';
} else {
    // Auto-detect: if system is dark, use dark; otherwise light
    $current_theme = 'light'; // Default to light
}
?>
<!-- Theme Switcher CSS -->
<style>
    /* Theme Variables */
    :root {
        --bg-light: #f8f9fa;
        --bg-dark: #111827;
        --surface: #ffffff;
        --surface-dark: #1f2937;
        --text-light: #212529;
        --text-dark: #f9fafb;
        --muted: #6b7280;
        --border: #e5e7eb;
        --border-dark: #374151;
        --accent: #3b82f6;
        --accent-dark: #60a5fa;
        --success: #10b981;
        --warning: #f59e0b;
        --error: #ef4444;
    }
    
    [data-theme="dark"] {
        --bg-light: #111827;
        --bg-dark: #1f2937;
        --surface: #1f2937;
        --text-light: #f9fafb;
        --text-dark: #111827;
        --muted: #9ca3af;
        --border: #374151;
        --accent: #60a5fa;
    }
    
    /* Theme Toggle Button */
    .theme-toggle {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        border: none;
        background: transparent;
        cursor: pointer;
        position: relative;
        flex-shrink: 0;
        transition: background-color 0.2s ease;
    }
    
    .theme-toggle:hover {
        background-color: var(--border);
    }
    
    .theme-toggle.light-mode {
        inset: 0;
    }
    
    .theme-toggle.light-mode .bg-light,
    .theme-toggle.light-mode .bg-dark {
        display: block;
    }
    
    .theme-toggle.light-mode .bg-light {
        position: absolute;
        inset: 0;
        background-color: #fff;
        border-radius: 10px;
        z-index: 1;
    }
    
    .theme-toggle.light-mode .bg-dark {
        display: none;
    }
    
    .theme-toggle.light-mode .moon {
        display: none;
    }
    
    .theme-toggle.light-mode .sun {
        display: block;
        position: absolute;
        inset: 0;
        margin: auto;
        width: 20px;
        height: 20px;
        background: #fbbf24;
        border-radius: 50%;
    }
    
    .theme-toggle.dark-mode {
        inset: 0;
    }
    
    .theme-toggle.dark-mode .bg-light,
    .theme-toggle.dark-mode .bg-dark {
        display: block;
    }
    
    .theme-toggle.dark-mode .bg-light {
        display: none;
    }
    
    .theme-toggle.dark-mode .bg-dark {
        position: absolute;
        inset: 0;
        background-color: #000;
        border-radius: 10px;
        z-index: 1;
    }
    
    .theme-toggle.dark-mode .moon {
        display: block;
        position: absolute;
        inset: 0;
        margin: auto;
        width: 20px;
        height: 20px;
        background: #fbbf24;
        border-radius: 50%;
    }
    
    .theme-toggle.dark-mode .sun {
        display: none;
    }
    
    .mode-indicator {
        position: absolute;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        transition: left 0.3s ease, right 0.3s ease;
    }
    
    .mode-indicator.sun {
        right: 8px;
        left: auto;
    }
    
    .mode-indicator.moon {
        left: 8px;
        right: auto;
    }
</style>

<body data-theme="<?= $current_theme ?>">

    <!-- Theme Toggle -->
    <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme" onclick="toggleTheme()">
        <span class="mode-indicator sun" id="mode-sun"></span>
        <span class="mode-indicator moon" id="mode-moon"></span>
        <span class="bg-light"></span>
        <span class="bg-dark"></span>
    </button>
    
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            
            if (currentTheme === 'dark') {
                html.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
                document.getElementById('mode-sun').style.display = 'none';
                document.getElementById('mode-moon').style.display = 'block';
            } else {
                html.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                document.getElementById('mode-sun').style.display = 'block';
                document.getElementbyId('mode-moon').style.display = 'none';
            }
        }
        
        // Check for saved theme or system preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            const html = document.documentElement;
            
            if (savedTheme) {
                html.setAttribute('data-theme', savedTheme);
                if (savedTheme === 'dark') {
                    document.getElementById('mode-sun').style.display = 'block';
                    document.getElementById('mode-moon').style.display = 'none';
                } else {
                    document.getElementById('mode-sun').style.display = 'none';
                    document.getElementById('mode-moon').style.display = 'block';
                }
            } else if (prefersDark) {
                html.setAttribute('data-theme', 'dark');
                document.getElementById('mode-sun').style.display = 'block';
                document.getElementById('mode-moon').style.display = 'none';
            } else {
                html.setAttribute('data-theme', 'light');
                document.getElementById('mode-sun').style.display = 'none';
                document.getElementById('mode-moon').style.display = 'block';
            }
        });
    </script>