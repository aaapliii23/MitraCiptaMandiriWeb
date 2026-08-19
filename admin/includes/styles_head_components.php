<style>
        /* Main Content */
        .main-content { 
            margin-left: 306px; 
            padding: 40px; 
            transition: all 0.3s; 
        }
        
        /* Compact page headings (matches greeting + all page titles) */
        .main-content h2:not(.stat-number) {
            font-size: 1.5rem;
        }
        
        .stat-card .card-body {
            padding: 20px;
        }
        
        /* Modern Design System Overhaul */
        .card { 
            border-radius: 1.25rem; 
            border: 1px solid rgba(226, 232, 240, 0.6); 
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04); 
            background: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -10px rgba(37, 99, 235, 0.12);
            border-color: rgba(37, 99, 235, 0.12);
        }
        
        /* Stat Cards — Content > Decoration */
        .stat-card {
            border: none;
            overflow: hidden;
            position: relative;
        }
        .stat-icon {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.16);
            border-radius: 0.8rem;
            font-size: 1rem;
        }
        .stat-number {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.02em;
        }
        .stat-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            opacity: 0.78;
            margin-top: 2px;
        }
        .stat-float {
            position: absolute;
            right: -10px;
            bottom: -12px;
            font-size: 4.75rem;
            opacity: 0.1;
            transform: rotate(-8deg);
            pointer-events: none;
        }
        .stat-card:hover .stat-float {
            opacity: 0.18;
            transform: rotate(-8deg) scale(1.08);
        }

        /* Modern Table */
        .table-responsive { 
            border-radius: 1.25rem; 
            border: 1px solid rgba(226, 232, 240, 0.8);
            background: #fff;
        }
        .table { margin-bottom: 0; }
        .table thead th { 
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 1px;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .table tbody td { 
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f8fafc;
            color: #334155;
            font-size: 0.9rem;
        }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover { background-color: #fcfdfe; }

        /* Modern Badges */
        .badge {
            padding: 0.5rem 1rem;
            font-weight: 700;
            border-radius: 50rem;
            font-size: 0.75rem;
        }
        .badge-soft-warning { background: #fffbeb; color: #d97706; }
        .badge-soft-success { background: #f0fdf4; color: #16a34a; }
        .badge-soft-primary { background: #eff6ff; color: #2563eb; }
        .badge-soft-danger { background: #fef2f2; color: #dc2626; }

        /* Action Buttons */
        .btn-action {
            width: 38px;
            height: 38px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            transition: all 0.2s;
        }
        .btn-action:hover { transform: scale(1.1); }

        /* Images */
        .image-preview {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 1rem;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        @media screen and (max-width: 991px) {
            .sidebar { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 20px 20px 100px 20px !important; }
            .card { border-radius: 1rem; }
            .table thead { display: none; } /* Hide headers on very small mobile if card fallback is used, but for now we keep table-responsive */
        }
</style>
