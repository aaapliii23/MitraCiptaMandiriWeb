
<style>
    /* Premium Bottom Navbar Styling - Matching User Request */
    .mcm-bottom-nav {
        display: none; /* Default hidden for desktop */
    }

    @media (max-width: 991px) {
        .mcm-bottom-nav {
            display: flex !important;
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(20px) !important;
            height: 70px !important;
            justify-content: space-around !important;
            align-items: center !important;
            z-index: 999999 !important;
            box-shadow: 0 -10px 25px rgba(0, 0, 0, 0.05) !important;
            border-top: 1px solid rgba(0,0,0,0.05) !important;
            padding-bottom: env(safe-area-inset-bottom) !important;
        }

        .mcm-nav-item {
            position: relative;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            text-decoration: none !important;
            color: #64748b !important; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
            flex: 1 !important;
            z-index: 2;
        }

        /* Active Indicator Glow */
        .mcm-nav-item.active {
            color: #2563eb !important;
            transform: translateY(-8px);
        }

        .mcm-nav-item.active .icon-wrapper {
            background: linear-gradient(135deg, #2563eb, #3b82f6) !important;
            color: #ffffff !important;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3) !important;
            transform: scale(1.1);
        }

        /* Continuous Motion for Active Icon */
        @keyframes float-active {
            0% { transform: translateY(0) scale(1.1); }
            50% { transform: translateY(-5px) scale(1.15); }
            100% { transform: translateY(0) scale(1.1); }
        }

        .mcm-nav-item.active .icon-wrapper {
            animation: float-active 3s ease-in-out infinite !important;
        }

        .mcm-nav-item .icon-wrapper {
            width: 42px !important;
            height: 42px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 14px !important;
            font-size: 1.2rem !important;
            margin-bottom: 4px !important;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        }

        /* Idle Animation */
        @keyframes breathe {
            0% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 0.8; }
        }

        .mcm-nav-item:not(.active) .icon-wrapper {
            animation: breathe 3s ease-in-out infinite;
        }

        .mcm-nav-item span {
            font-size: 0.6rem !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .mcm-nav-item.active span {
            opacity: 1 !important;
            color: #1e293b !important;
        }

        /* Custom Progress for AJAX */
        .ajax-progress {
            position: fixed;
            top: 0; left: 0;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #7c3aed, #db2777);
            z-index: 999999;
            width: 0;
            transition: width 0.3s ease;
            box-shadow: 0 0 15px rgba(37, 99, 235, 0.5);
        }
    }
</style>
<div class="ajax-progress" id="ajaxProgress"></div>
