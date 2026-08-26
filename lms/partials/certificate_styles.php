<style id="printPageSize">
    @page {
        size: <?php echo $orientation === 'landscape' ? '297mm 210mm' : '210mm 297mm'; ?>;
        margin: 0;
    }
</style>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Cinzel:wght@600;700;800&display=swap');
    
    :root {
        --cert-accent: <?php echo htmlspecialchars($template['accent_color']); ?>;
        --cert-gold: #d4af37;
        --cert-navy: #0c4a6e;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: #e2e8f0;
        color: #1e293b;
        margin: 0;
        padding: 0;
    }

    .cert-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 36px;
        padding: 30px 15px 60px;
    }

    /* Certificate Base Page */
    .cert-page {
        background: #ffffff;
        position: relative;
        box-shadow: 0 15px 45px rgba(0,0,0,0.12);
        box-sizing: border-box;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Landscape Mode */
    .cert-wrapper.orientation-landscape .cert-page {
        width: 297mm;
        height: 210mm;
        max-width: 1040px;
        padding: 12mm 16mm;
    }

    .cert-wrapper.orientation-landscape .cert-border {
        border: 4px solid var(--cert-navy);
        height: 100%;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 16px 28px;
        position: relative;
    }

    .cert-wrapper.orientation-landscape .cert-recipient-name {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--cert-navy);
        border-bottom: 2px solid var(--cert-gold);
        display: inline-block;
        padding: 0 24px 2px;
    }

    .cert-wrapper.orientation-landscape .cert-class-name {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--cert-navy);
    }

    .cert-wrapper.orientation-landscape .cert-seal {
        width: 76px;
        height: 76px;
    }

    /* Portrait Mode (Full A4 210mm x 297mm) */
    .cert-wrapper.orientation-portrait .cert-page {
        width: 210mm;
        height: 297mm;
        max-width: 794px;
        padding: 16mm 16mm;
    }

    .cert-wrapper.orientation-portrait .cert-border {
        border: 4.5px solid var(--cert-navy);
        height: 100%;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 28px 24px;
        position: relative;
    }

    .cert-wrapper.orientation-portrait .cert-recipient-name {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--cert-navy);
        border-bottom: 2.5px solid var(--cert-gold);
        display: inline-block;
        padding: 0 28px 4px;
        margin-top: 8px;
        margin-bottom: 8px;
    }

    .cert-wrapper.orientation-portrait .cert-class-name {
        font-size: 1.9rem;
        font-weight: 800;
        color: var(--cert-navy);
        margin: 6px 0;
    }

    .cert-wrapper.orientation-portrait .cert-seal {
        width: 88px;
        height: 88px;
    }

    .cert-wrapper.orientation-portrait .portrait-spacer {
        padding: 12px 0;
    }

    .cert-wrapper.orientation-portrait .units-table th,
    .cert-wrapper.orientation-portrait .units-table td {
        padding: 10px 14px;
    }

    /* Inner Gold Border */
    .cert-border::before {
        content: '';
        position: absolute;
        inset: 4px;
        border: 1.5px solid var(--cert-gold);
        pointer-events: none;
    }

    .cert-title-cinzel {
        font-family: 'Cinzel', serif;
        letter-spacing: 3px;
    }

    .cert-ribbon {
        background: linear-gradient(135deg, var(--cert-navy), #0ea5e9);
        color: #fff;
        display: inline-block;
        padding: 6px 28px;
        border-radius: 50px;
        letter-spacing: 2.5px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .cert-seal {
        border-radius: 50%;
        background: radial-gradient(circle, #fffbe0 0%, #fef08a 60%, #eab308 100%);
        border: 3px dashed #b45309;
        box-shadow: 0 4px 10px rgba(217, 119, 6, 0.25);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #92400e;
        font-size: 0.58rem;
        font-weight: 800;
        text-align: center;
        line-height: 1.1;
    }

    /* Units Table */
    .units-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.82rem;
    }

    .units-table th {
        background: #0c4a6e;
        color: #ffffff;
        font-weight: 700;
        padding: 7px 12px;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        border: 1px solid #0c4a6e;
    }

    .units-table td {
        padding: 6.5px 12px;
        border: 1px solid #cbd5e1;
        color: #334155;
    }

    .units-table tbody tr:nth-child(even) {
        background: #f8fafc;
    }

    /* Print Media Settings */
    @media print {
        body { 
            background: #fff !important; 
            margin: 0 !important;
            padding: 0 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .no-print { display: none !important; }
        .cert-container { 
            padding: 0 !important; 
            margin: 0 !important;
            gap: 0 !important; 
            display: block !important;
        }
        .cert-page {
            box-shadow: none !important;
            margin: 0 !important;
            overflow: hidden !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .cert-wrapper.orientation-landscape .cert-page {
            width: 297mm !important;
            height: 210mm !important;
            max-width: 297mm !important;
            max-height: 210mm !important;
        }
        .cert-wrapper.orientation-portrait .cert-page {
            width: 210mm !important;
            height: 297mm !important;
            max-width: 210mm !important;
            max-height: 297mm !important;
        }
        /* Pengaman portrait: padatkan tabel unit agar pasti muat 1 halaman A4 */
        .cert-wrapper.orientation-portrait .cert-border {
            padding: 18px 20px !important;
        }
        .cert-wrapper.orientation-portrait .units-table {
            font-size: 0.72rem !important;
        }
        .cert-wrapper.orientation-portrait .units-table th {
            padding: 5px 8px !important;
            font-size: 0.62rem !important;
        }
        .cert-wrapper.orientation-portrait .units-table td {
            padding: 4px 8px !important;
        }
        .cert-page.page-front {
            page-break-after: always !important;
            break-after: page !important;
        }
        .cert-page.page-back {
            page-break-before: always !important;
            break-before: page !important;
            page-break-after: avoid !important;
            break-after: avoid !important;
        }
    }
</style>
