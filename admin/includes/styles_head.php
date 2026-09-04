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
        
        html {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        html::-webkit-scrollbar { width: 8px; height: 8px; }
        html::-webkit-scrollbar-track { background: #f1f5f9; }
        html::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; border: 2px solid #f1f5f9; }
        html::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
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

        .stat-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 24px -8px rgba(2, 6, 23, 0.18) !important;
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
            width: 306px; 
            padding: 20px; 
            position: fixed; 
            top: 0; left: 0; 
            z-index: 1000; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            box-shadow: 4px 0 24px rgba(0,0,0,0.05);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.1) transparent;
        }
        
        /* Sidebar inner alignment — one 20px grid for all elements */
        .sidebar .p-4 {
            padding: 0 0 16px;
        }
        .sidebar > .p-4.mt-auto {
            padding: 20px 0 0;
        }
        .sidebar .px-4 {
            padding: 16px 0;
        }
        .sidebar .nav.flex-column {
            padding-left: 0;
            padding-right: 0;
            margin-bottom: 0;
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
            padding: 10px 12px; 
            font-size: 0.875rem; 
            font-weight: 600; 
            border-radius: 10px; 
            margin: 0 0 4px; 
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            border: 1px solid transparent;
        }

        .sidebar .nav-link i { 
            width: 26px; 
            font-size: 1.05rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover { 
            background-color: rgba(255,255,255,0.08); 
            color: #fff; 
            border-color: rgba(255,255,255,0.1);
        }

        .sidebar .nav-link:hover i {
            color: var(--secondary-color);
        }

        .sidebar .nav-link.active { 
            background: linear-gradient(135deg, #2563eb, #4f46e5); 
            color: #fff !important; 
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
            border-color: rgba(255,255,255,0.2);
        }

        .sidebar .nav-link.active i {
            color: #fff !important;
        }

        .sidebar-group-toggle {
            width: 100%;
            background: transparent;
            border: none;
            color: #64748b;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 14px 12px 6px;
            margin-top: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .sidebar .sidebar-group-toggle:first-child {
            margin-top: 4px;
        }

        .sidebar-group-toggle:hover {
            color: #cbd5e1;
        }

        .sidebar-group-toggle i {
            font-size: 0.7rem;
            transition: transform 0.3s ease;
        }

        .sidebar-group-toggle[aria-expanded="true"] i {
            transform: rotate(180deg);
        }
        
        .finance-type-btn {
            cursor: pointer;
            white-space: nowrap;
            text-align: center;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
        @media (max-width: 400px) {
            .finance-type-btn {
                font-size: 0.78rem;
                padding: 0.5rem 0.4rem;
            }
        }
        @media (max-width: 576px) {
            #financeModal .modal-body { -webkit-overflow-scrolling: touch; }
            #financeModal input[type="text"],
            #financeModal input[type="number"],
            #financeModal textarea,
            #financeModal select { font-size: 16px !important; }
            #financeModal .modal-footer {
                position: sticky;
                bottom: 0;
                background: #f8fafc;
                z-index: 2;
                border-top: 1px solid #e2e8f0;
            }
        }

        @media print {
            .no-print, 
            .sidebar, 
            .mcm-bottom-nav, 
            .d-md-none, 
            .btn-group, 
            #reportYear, 
            .btn-action,
            .btn, 
            select,
            button,
            .modal,
            .modal-backdrop,
            header,
            footer { 
                display: none !important; 
            }
            
            body, html {
                background: white !important;
                color: black !important;
                height: auto !important;
                overflow: visible !important;
            }
            
            .main-content { 
                margin: 0 !important; 
                padding: 0 !important; 
                width: 100% !important; 
                position: static !important;
                overflow: visible !important;
                height: auto !important;
                min-height: auto !important;
            }
            
            .card { 
                border: 1px solid #ddd !important; 
                box-shadow: none !important; 
                page-break-inside: avoid;
            }
        }
</style>
