<style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        /* Pastikan modal scrollable dengan <form> di dalamnya tetap bisa scroll */
        .modal-dialog-scrollable .modal-content > form {
            display: flex;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }
        
        :root {
            --primary-color: #2563eb;
            --secondary-color: #3b82f6;
            --dark-bg: #0f172a;
            --sidebar-bg: #1e293b;
        }
        
        html, body {
            height: auto !important;
            min-height: 100% !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
        }

        body { 
            background-color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #334155;
            -webkit-tap-highlight-color: transparent;
            overflow-x: hidden;
        }

        /* Premium Animations */
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(5deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }

        .float-icon {
            animation: float 6s ease-in-out infinite;
        }

        .stat-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        .stat-card:hover .position-absolute {
            opacity: 0.2 !important;
            transform: scale(1.1) rotate(10deg);
        }
        
        
        /* Premium Text Styling */
        .gradient-text {
            background: linear-gradient(135deg, #2563eb, #7c3aed, #db2777);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .greet-line {
            height: 4px;
            width: 40px;
            background: linear-gradient(90deg, #2563eb, #3b82f6);
            border-radius: 10px;
            margin-top: 8px;
            transition: width 0.5s ease;
        }

        .mb-5:hover .greet-line {
            width: 80px;
        }

        /* Bottom Nav Upgrades */
        .mcm-bottom-nav {
            padding: 0 15px !important;
        }

        .mcm-nav-item .icon-wrapper {
            position: relative;
            z-index: 2;
        }

        @keyframes pulse-icon {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .mcm-nav-item:active .icon-wrapper {
            animation: pulse-icon 0.3s ease-out;
        }
        .sidebar { 
            height: 100vh; 
            background-color: var(--dark-bg); 
            width: 280px; 
            position: fixed; 
            top: 0; left: 0; 
            z-index: 1000; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            box-shadow: 4px 0 24px rgba(0,0,0,0.05);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.1) transparent;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
        .sidebar .nav-link { 
            color: #94a3b8; 
            padding: 16px 24px; 
            font-weight: 600; 
            border-radius: 16px; 
            margin: 0 16px 10px; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            border: 1px solid transparent;
        }

        .sidebar .nav-link i { 
            width: 32px; 
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover { 
            background-color: rgba(255,255,255,0.08); 
            color: #fff; 
            transform: translateX(8px);
            border-color: rgba(255,255,255,0.1);
        }

        .sidebar .nav-link:hover i {
            transform: scale(1.2);
            color: var(--secondary-color);
        }

        .sidebar .nav-link.active { 
            background: linear-gradient(135deg, #2563eb, #4f46e5); 
            color: #fff !important; 
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
            border-color: rgba(255,255,255,0.2);
        }

        /* Shimmer Effect for Active Link */
        .sidebar .nav-link.active::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: skewX(-25deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            50% { left: 150%; }
            100% { left: 150%; }
        }

        .sidebar .nav-link.active i {
            color: #fff !important;
            filter: drop-shadow(0 0 5px rgba(255,255,255,0.5));
        }

        /* Continuous Idle Animation for Sidebar Icons */
        @keyframes side-icon-pulse {
            0% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.15); opacity: 1; }
            100% { transform: scale(1); opacity: 0.7; }
        }

        .sidebar .nav-link:not(.active) i {
            animation: side-icon-pulse 4s ease-in-out infinite;
        }

        .sidebar .nav-link:not(.active):hover i {
            animation: none;
        }
        
</style>
