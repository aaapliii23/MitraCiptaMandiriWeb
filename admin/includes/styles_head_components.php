<style>
        /* Main Content */
        .main-content { 
            margin-left: 280px; 
            padding: 40px; 
            transition: all 0.3s; 
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
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -10px rgba(37, 99, 235, 0.1);
            border-color: rgba(37, 99, 235, 0.1);
        }
        
        .stat-card {
            border: none;
            overflow: hidden;
            position: relative;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
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

        @media (max-width: 991px) {
            .sidebar { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 20px 20px 100px 20px !important; }
            .card { border-radius: 1rem; }
            .table thead { display: none; } /* Hide headers on very small mobile if card fallback is used, but for now we keep table-responsive */
        }
</style>
