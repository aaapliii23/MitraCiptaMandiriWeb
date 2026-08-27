<style id="printPageSize">
    @page {
        size: <?php echo $orientation === 'landscape' ? '297mm 210mm' : '210mm 297mm'; ?>;
        margin: 0;
    }
</style>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Cinzel:wght@600;700;800&family=Caveat:wght@600;700&family=Dancing+Script:wght@600;700&display=swap');
    
    :root {
        --cert-accent: <?php echo htmlspecialchars($template['accent_color']); ?>;
        --cert-gold: #d4af37;
        --cert-gold-light: #f0d27a;
        --cert-navy: #0c4a6e;
        --cert-navy-dark: #08324a;
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
        padding: 10mm 12mm;
    }

    .cert-wrapper.orientation-landscape .cert-border {
        border: 3px solid var(--cert-navy);
        height: 100%;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 14px 92px 14px 28px;
        position: relative;
        overflow: hidden;
    }

    .cert-wrapper.orientation-landscape .cert-recipient-name {
        font-size: 2.15rem;
        font-weight: 800;
        color: var(--cert-navy);
        letter-spacing: 0.3px;
        border-bottom: 2px solid var(--cert-gold);
        display: inline-block;
        padding: 0 18px 2px 0;
        line-height: 1.15;
    }

    .cert-wrapper.orientation-landscape .cert-class-name {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--cert-navy);
        line-height: 1.25;
    }

    .cert-wrapper.orientation-landscape .cert-seal { width: 76px; height: 76px; }

    /* Portrait Mode (Full A4 210mm x 297mm) */
    .cert-wrapper.orientation-portrait .cert-page {
        width: 210mm;
        height: 297mm;
        max-width: 794px;
        padding: 12mm 10mm;
    }

    .cert-wrapper.orientation-portrait .cert-border {
        border: 3.5px solid var(--cert-navy);
        height: 100%;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 22px 78px 22px 22px;
        position: relative;
        overflow: hidden;
    }

    .cert-wrapper.orientation-portrait .cert-recipient-name {
        font-size: 2rem;
        font-weight: 800;
        color: var(--cert-navy);
        border-bottom: 2.5px solid var(--cert-gold);
        display: inline-block;
        padding: 0 18px 4px 0;
        margin-top: 4px;
        margin-bottom: 4px;
        line-height: 1.2;
    }

    .cert-wrapper.orientation-portrait .cert-class-name {
        font-size: 1.45rem;
        font-weight: 800;
        color: var(--cert-navy);
        margin: 6px 0 4px;
        line-height: 1.25;
    }

    .cert-wrapper.orientation-portrait .cert-seal { width: 88px; height: 88px; }
    .cert-wrapper.orientation-portrait .portrait-spacer { padding: 8px 0; }
    .cert-wrapper.orientation-portrait .cert-front-title { font-size: 1.85rem !important; }
    .cert-wrapper.orientation-portrait .cert-front-footer { gap: 12px; max-width: 80%; }
    .cert-wrapper.orientation-portrait .cert-front-body { max-width: 72%; }
    .cert-wrapper.orientation-portrait .cert-ribbon-v { width: 58px; right: 10px; }
    .cert-wrapper.orientation-portrait .cert-seal-badge { width: 120px; height: 120px; right: 30px; }
    .cert-wrapper.orientation-portrait .cert-seal-badge svg.seal-svg { width: 120px; height: 120px; }
    .cert-wrapper.orientation-portrait .cert-seal-badge .seal-center-logo { width: 48px; height: 48px; }
    .cert-wrapper.orientation-portrait .cert-seal-badge .seal-center-logo img { width: 36px; height: 36px; }
    .cert-wrapper.orientation-portrait #certQr { width: 78px; height: 78px; }
    .cert-wrapper.orientation-portrait #certQr img, .cert-wrapper.orientation-portrait #certQr canvas { width: 78px !important; height: 78px !important; }
    .cert-wrapper.orientation-portrait .cert-qr-wrap { transform: scale(0.95); transform-origin: right bottom; }

    .cert-wrapper.orientation-portrait .units-table th,
    .cert-wrapper.orientation-portrait .units-table td {
        padding: 10px 14px;
    }

    /* Inner Gold Border */
    .cert-border::before {
        content: '';
        position: absolute;
        inset: 4px;
        border: 1.2px solid var(--cert-gold);
        pointer-events: none;
        z-index: 1;
    }
    .cert-border::after {
        content: '';
        position: absolute;
        inset: 7px;
        border: 0.7px solid rgba(212,175,55,0.35);
        pointer-events: none;
        z-index: 1;
    }

    .cert-title-cinzel {
        font-family: 'Cinzel', serif;
        letter-spacing: 3px;
    }

    /* Legacy ribbon pill (kept for fallback, not used in new front) */
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

    /* ===== NEW FRONT DESIGN: Guilloche, Ribbon, Seal, QR ===== */
    .cert-page.page-front {
        background: #ffffff;
    }
    .cert-front-border {
        text-align: left;
        z-index: 1;
    }
    .cert-guilloche {
        position: absolute;
        inset: 0;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
        opacity: 1;
    }
    .cert-guilloche svg {
        width: 100%;
        height: 100%;
        display: block;
        opacity: 0.055;
    }
    /* Ribbon vertical navy with gold notch */
    .cert-ribbon-v {
        position: absolute;
        top: 0;
        right: 14px;
        width: 68px;
        height: 100%;
        background: linear-gradient(180deg, var(--cert-navy) 0%, #0a3d5b 100%);
        z-index: 2;
        box-shadow: -4px 0 14px rgba(12,74,110,0.18);
        border-left: 1px solid rgba(255,255,255,0.18);
        border-right: 1px solid rgba(255,255,255,0.10);
    }
    .cert-ribbon-v::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 22px;
        background: var(--cert-gold);
        clip-path: polygon(0 0, 50% 58%, 100% 0, 100% 100%, 0 100%);
        box-shadow: 0 -2px 8px rgba(0,0,0,0.12);
    }
    .cert-ribbon-v::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, rgba(255,255,255,0.08) 0%, transparent 45%, rgba(255,255,255,0.06) 100%);
        pointer-events: none;
    }
    /* Seal badge overlapping ribbon */
    .cert-seal-badge {
        position: absolute;
        right: 38px;
        top: 50%;
        transform: translateY(-50%);
        width: 138px;
        height: 138px;
        z-index: 3;
        background: #ffffff;
        border-radius: 50%;
        box-shadow: 0 8px 24px rgba(12,74,110,0.22), 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        overflow: visible;
    }
    .cert-seal-badge svg.seal-svg {
        width: 138px;
        height: 138px;
        display: block;
        border-radius: 50%;
    }
    .cert-seal-badge .seal-center-logo {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 54px;
        height: 54px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 0 0 1px rgba(12,74,110,0.08);
        overflow: hidden;
    }
    .cert-seal-badge .seal-center-logo img {
        width: 42px;
        height: 42px;
        object-fit: contain;
    }
    /* Front header brand */
    .cert-front-header {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 4px;
    }
    .cert-brand {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .cert-brand img {
        height: 46px;
        width: auto;
        object-fit: contain;
        filter: drop-shadow(0 1px 2px rgba(0,0,0,0.04));
    }
    .cert-brand-text {
        display: flex;
        flex-direction: column;
        justify-content: center;
        border-left: 2.5px solid var(--cert-navy);
        padding-left: 10px;
        height: 42px;
    }
    .cert-brand-text .brand-title {
        font-family: 'Cinzel', serif;
        font-weight: 800;
        font-size: 0.92rem;
        letter-spacing: 1.2px;
        color: var(--cert-navy);
        line-height: 1.05;
    }
    .cert-brand-text .brand-sub {
        font-size: 0.58rem;
        letter-spacing: 1.8px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        margin-top: 2px;
    }
    .cert-front-title {
        font-family: 'Cinzel', serif;
        font-weight: 800;
        font-size: 2.05rem;
        letter-spacing: 3.5px;
        color: var(--cert-navy);
        line-height: 1.1;
        margin: 10px 0 6px;
        text-align: left;
    }
    .cert-front-title .gold { color: var(--cert-gold); }
    .cert-front-reg {
        font-size: 0.68rem;
        letter-spacing: 1.1px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .cert-front-reg strong {
        color: var(--cert-navy);
        font-weight: 800;
        letter-spacing: 0.8px;
    }
    .cert-front-body {
        position: relative;
        z-index: 1;
        text-align: left;
        padding: 6px 0;
        max-width: 78%;
    }
    .cert-presented-label {
        font-size: 0.66rem;
        letter-spacing: 2.2px;
        color: #94a3b8;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .cert-recipient-email {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 2px;
        letter-spacing: 0.2px;
    }
    .cert-desc {
        margin-top: 12px;
        font-size: 0.78rem;
        color: #64748b;
        line-height: 1.5;
    }
    .cert-desc strong { color: #1e293b; }
    .cert-front-body .cert-class-name {
        margin: 4px 0 6px;
        text-align: left;
    }
    .cert-category-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(12,74,110,0.07);
        border: 1px solid rgba(12,74,110,0.12);
        color: var(--cert-navy);
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.6px;
        padding: 5px 12px;
        border-radius: 999px;
        text-transform: uppercase;
    }
    .cert-front-footer {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 14px;
        margin-top: 10px;
        max-width: 88%;
    }
    .cert-sign {
        text-align: center;
        flex: 1;
        min-width: 0;
    }
    .cert-sign-left, .cert-sign-right {
        flex: 0 1 170px;
    }
    .cert-center-meta {
        flex: 1;
        text-align: center;
        padding-bottom: 6px;
    }
    .cert-signature-hand {
        font-family: 'Caveat', 'Dancing Script', cursive;
        font-size: 1.55rem;
        color: #1e3a5f;
        line-height: 1;
        transform: rotate(-1.5deg);
        margin-bottom: 2px;
        font-weight: 700;
    }
    .cert-sign-line {
        width: 150px;
        height: 1.5px;
        background: linear-gradient(90deg, transparent, #334155 18%, #334155 82%, transparent);
        margin: 6px auto 6px;
        opacity: 0.85;
    }
    .cert-sign-name {
        font-size: 0.72rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.25;
    }
    .cert-sign-role {
        font-size: 0.62rem;
        color: #64748b;
        font-weight: 600;
        margin-top: 2px;
        letter-spacing: 0.3px;
    }
    .cert-issued-label {
        font-size: 0.60rem;
        letter-spacing: 1.6px;
        text-transform: uppercase;
        color: #94a3b8;
        font-weight: 700;
        margin-bottom: 2px;
    }
    .cert-issued-org {
        font-size: 0.78rem;
        font-weight: 800;
        color: var(--cert-navy);
        letter-spacing: 0.4px;
    }
    .cert-issued-date {
        font-size: 0.68rem;
        color: #475569;
        font-weight: 600;
        margin-top: 3px;
    }
    .cert-qr-wrap {
        flex: 0 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        z-index: 2;
        margin-left: 8px;
    }
    .cert-qr-link {
        display: block;
        text-decoration: none;
        background: #fff;
        padding: 6px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(12,74,110,0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .cert-qr-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(12,74,110,0.14);
        border-color: var(--cert-gold);
    }
    #certQr {
        width: 88px;
        height: 88px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border-radius: 6px;
        overflow: hidden;
        line-height: 0;
    }
    #certQr img, #certQr canvas {
        width: 88px !important;
        height: 88px !important;
        display: block;
        border-radius: 4px;
    }
    .cert-qr-caption {
        font-size: 0.58rem;
        letter-spacing: 0.9px;
        text-transform: uppercase;
        font-weight: 700;
        color: #94a3b8;
        text-align: center;
        line-height: 1;
    }
    /* Responsive tweak for front footer on narrow landscape */
    @media (max-width: 900px) {
        .cert-front-body { max-width: 72%; }
        .cert-front-footer { max-width: 84%; }
        .cert-seal-badge { width: 118px; height: 118px; right: 32px; }
        .cert-seal-badge svg.seal-svg { width: 118px; height: 118px; }
        .cert-ribbon-v { width: 56px; right: 10px; }
        #certQr, #certQr img, #certQr canvas { width: 78px !important; height: 78px !important; }
        #certQr { width: 78px; height: 78px; }
        .cert-front-title { font-size: 1.75rem !important; }
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
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .cert-guilloche, .cert-ribbon-v, .cert-seal-badge, #certQr, .cert-qr-link, .cert-qr-wrap {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
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
        .cert-wrapper.orientation-portrait .cert-border {
            padding: 18px 78px 18px 20px !important;
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
