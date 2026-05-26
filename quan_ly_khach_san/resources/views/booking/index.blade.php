<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khách sạn - Nhóm 4</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap&subset=vietnamese" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --bg: #f3f7fb;
            --ink-900: #10243e;
            --ink-700: #17324f;
            --ink-500: #334e68;
            --line: #c7d6e6;
            --brand: #0b6f68;
            --brand-dark: #084f4b;
            --accent: #d97706;
            --gold: #d8a84f;
            --surface: #ffffff;
            --surface-soft: #f7fbff;
            --shadow-soft: 0 16px 34px rgba(16, 42, 67, 0.08);
            --shadow-deep: 0 24px 54px rgba(9, 28, 50, 0.24);
            --content-max: 100%;
        }

        * {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: var(--ink-900);
            background:
                radial-gradient(circle at 12% 8%, #d9f4ff 0, transparent 32%),
                radial-gradient(circle at 88% 0%, #dbe7ff 0, transparent 32%),
                radial-gradient(circle at 95% 95%, #d8f8ef 0, transparent 30%),
                linear-gradient(180deg, #eef7ff 0%, #f6fafc 48%, #eef6f7 100%),
                var(--bg);
            line-height: 1.65;
            font-weight: 500;
        }

        .shell {
            width: 100%;
            max-width: var(--content-max);
            margin: 0 auto;
            padding: clamp(18px, 1.6vw, 28px) clamp(18px, 2.2vw, 42px) 54px;
            display: grid;
            gap: 18px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: calc(-1 * clamp(18px, 1.6vw, 28px)) calc(-1 * clamp(18px, 2.2vw, 42px)) 0;
            padding: 14px clamp(22px, 4vw, 70px);
            border-radius: 0 0 18px 18px;
            border: 0;
            background: linear-gradient(90deg, #071b3a, #0f766e);
            box-shadow: 0 14px 30px rgba(7, 27, 58, 0.24);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            text-decoration: none;
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        .brand-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            color: #fff;
            background: rgba(255, 255, 255, 0.14);
        }

        .top-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .top-search-trigger {
            border: 1px solid rgba(255, 255, 255, 0.26);
            border-radius: 999px;
            min-height: 40px;
            padding: 9px 14px;
            color: #07324f;
            background: #e8f7ff;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 10px 22px rgba(4, 20, 36, 0.16);
        }

        .top-search-trigger:hover,
        .top-search-trigger[aria-expanded="true"] {
            color: #05283f;
            background: #fff;
        }

        .search-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1040;
            opacity: 0;
            pointer-events: none;
            background: rgba(5, 16, 30, 0.48);
            backdrop-filter: blur(5px);
            transition: opacity 0.22s ease;
        }

        body.search-panel-open {
            overflow: hidden;
        }

        body.auth-panel-open,
        body.search-panel-open {
            overflow: hidden;
        }

        body.auth-panel-open .search-backdrop,
        body.search-panel-open .search-backdrop {
            opacity: 1;
            pointer-events: auto;
        }

        .auth-panel {
            position: fixed;
            top: 86px;
            right: clamp(14px, 4vw, 70px);
            z-index: 1050;
            width: min(460px, calc(100vw - 28px));
            max-height: calc(100vh - 110px);
            border-radius: 22px;
            border: 1px solid rgba(255, 255, 255, 0.74);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(246, 250, 252, 0.96));
            box-shadow: 0 24px 56px rgba(5, 16, 30, 0.34);
            backdrop-filter: blur(14px);
            padding: 18px;
            overflow: auto;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(-12px) scale(0.97);
            transform-origin: top right;
            transition: opacity 0.22s ease, transform 0.22s ease, visibility 0.22s ease;
        }

        .auth-panel.is-active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }

        .auth-panel__head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }

        .auth-panel__head h2 {
            margin: 0;
            color: var(--ink-900);
            font-size: 1.35rem;
            font-weight: 800;
        }

        .auth-panel__head p {
            margin: 4px 0 0;
            color: var(--ink-500);
            font-size: 0.88rem;
            font-weight: 600;
        }

        .auth-panel__close {
            width: 36px;
            height: 36px;
            border: 0;
            border-radius: 999px;
            display: inline-grid;
            place-items: center;
            color: #17324f;
            background: #eef4f8;
        }

        .auth-panel__close:hover {
            background: #dcebf3;
        }

        .auth-panel .form-label {
            margin-bottom: 6px;
            color: #17324f;
            font-size: 0.86rem;
            font-weight: 800;
        }

        .auth-panel .form-control {
            min-height: 48px;
            border-radius: 12px;
            border-color: #c7d6e6;
            color: var(--ink-900);
            font-weight: 700;
        }

        .auth-panel__switch {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-top: 12px;
            color: var(--ink-500);
            font-size: 0.88rem;
            font-weight: 700;
        }

        .auth-panel__link {
            border: 0;
            padding: 0;
            color: var(--brand);
            background: transparent;
            font-weight: 800;
        }

        .btn-soft,
        .btn-danger-soft {
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            font-weight: 700;
            min-height: 40px;
            padding: 9px 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-soft:hover {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.32);
            background: rgba(255, 255, 255, 0.18);
        }

        .btn-danger-soft {
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #08345f;
            background: #e8f7ff;
        }

        .btn-danger-soft:hover {
            color: #08345f;
            background: #fff;
        }

        .quick-actions {
            position: absolute;
            left: clamp(22px, 4vw, 58px);
            bottom: clamp(22px, 4vw, 46px);
            z-index: 3;
            width: min(430px, calc(100% - 44px));
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            padding: 0;
        }

        .search-strip {
            position: relative;
            z-index: 2;
            width: 100%;
            justify-self: end;
            border-radius: 18px;
            border: 1px solid rgba(176, 213, 238, 0.95);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.97), rgba(242, 248, 255, 0.95));
            box-shadow: 0 20px 44px rgba(10, 31, 53, 0.22);
            backdrop-filter: blur(12px);
            padding: 10px;
        }

        .search-strip::before {
            content: "";
            display: block;
            height: 3px;
            border-radius: 999px;
            margin-bottom: 10px;
            background: linear-gradient(90deg, var(--brand), #38bdf8, var(--gold));
        }

        .quick-action {
            position: relative;
            overflow: hidden;
            min-height: 74px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.36);
            background: linear-gradient(135deg, rgba(7, 27, 58, 0.88), rgba(15, 118, 110, 0.84));
            color: #ffffff;
            padding: 14px 18px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 18px 42px rgba(3, 12, 26, 0.34);
            backdrop-filter: blur(14px);
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        }

        .quick-action::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 0% 0%, rgba(255, 255, 255, 0.24), transparent 34%),
                linear-gradient(90deg, rgba(216, 168, 79, 0.24), transparent 55%);
            opacity: 0.95;
            pointer-events: none;
        }

        .quick-action:hover {
            color: #ffffff;
            border-color: rgba(245, 213, 143, 0.74);
            box-shadow: 0 24px 56px rgba(3, 12, 26, 0.44);
            transform: translateX(6px);
        }

        .quick-action i {
            position: relative;
            z-index: 1;
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            color: #08345f;
            background: linear-gradient(135deg, #ffffff, #e8f7ff);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.22);
            font-size: 1.08rem;
        }

        .quick-action span {
            position: relative;
            z-index: 1;
        }

        .quick-action small {
            display: block;
            margin-bottom: 4px;
            color: #f5d58f;
            line-height: 1.1;
            font-size: 0.74rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .quick-action strong {
            display: block;
            color: #ffffff;
            line-height: 1.15;
            font-size: 1.06rem;
            font-weight: 900;
        }

                    .booking-showcase {
                position: relative;
                width: calc(100vw - 48px);
                max-width: 1600px;
                margin: 0 auto;
                display: grid;
                align-items: end;
                min-height: clamp(430px, 48vw, 620px);
                padding: 0;
                border-radius: 24px;
                overflow: hidden;
                box-shadow: var(--shadow-deep);
                background: #0d2338;
            }
        .showcase-room-card,
        .showcase-search-card {
            overflow: hidden;
        }

        .showcase-room-card {
            position: absolute;
            inset: 0;
            z-index: 0;
            min-height: 100%;
            background: #0d2338;
        }

        .showcase-room-image {
            width: 100%;
            height: 100%;
            min-height: 100%;
            object-fit: cover;
            display: block;
        }

        .showcase-room-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(5, 16, 30, 0.66) 0%, rgba(5, 16, 30, 0.36) 38%, rgba(5, 16, 30, 0.05) 72%),
                linear-gradient(180deg, rgba(5, 16, 30, 0.04) 0%, rgba(5, 16, 30, 0.34) 100%);
            pointer-events: none;
        }

        .showcase-room-copy {
            display: none;
        }

        .showcase-search-card {
            position: fixed;
            top: 86px;
            right: clamp(14px, 4vw, 70px);
            z-index: 1050;
            width: min(680px, calc(100vw - 28px));
            max-height: calc(100vh - 110px);
            border-radius: 22px;
            border: 1px solid rgba(255, 255, 255, 0.74);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(246, 250, 252, 0.92));
            padding: 16px;
            box-shadow: 0 24px 56px rgba(5, 16, 30, 0.34);
            backdrop-filter: blur(14px);
            overflow: auto;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(-12px) scale(0.97);
            transform-origin: top right;
            transition: opacity 0.22s ease, transform 0.22s ease, visibility 0.22s ease;
        }

        body.search-panel-open .showcase-search-card {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }

        .showcase-search-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 12px;
        }

        .showcase-search-head h2 {
            margin: 0;
            color: var(--ink-900);
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .showcase-search-head p {
            margin: 4px 0 0;
            color: var(--ink-500);
            font-size: 0.88rem;
            font-weight: 600;
            line-height: 1.55;
        }

        .showcase-search-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .search-panel-close {
            width: 36px;
            height: 36px;
            border: 0;
            border-radius: 999px;
            display: inline-grid;
            place-items: center;
            color: #17324f;
            background: #eef4f8;
        }

        .search-panel-close:hover {
            color: #071b3a;
            background: #dcebf3;
        }

        .showcase-search-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border-radius: 999px;
            padding: 8px 10px;
            color: #0f5f58;
            background: #eef8f6;
            font-size: 0.78rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .showcase-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            margin-bottom: 12px;
        }

        .showcase-stat {
            border: 1px solid #d7e3ef;
            border-radius: 14px;
            background: #fff;
            padding: 10px 12px;
        }

        .showcase-stat strong {
            display: block;
            color: var(--ink-900);
            font-size: 1.1rem;
            line-height: 1;
            font-weight: 800;
        }

        .showcase-stat span {
            display: block;
            margin-top: 5px;
            color: var(--ink-500);
            font-size: 0.74rem;
            font-weight: 700;
        }

        .booking-showcase .search-strip {
            box-shadow: none;
            border-color: #d1deea;
            background: #fff;
        }

        .filter-box {
            position: relative;
            width: 100%;
            margin: 0 auto;
            border-radius: 0;
            border: 0;
            background: transparent;
            padding: 0;
            box-shadow: none;
            color: var(--ink-900);
        }

        .filter-box::before {
            display: none;
        }

        .filter-box::after {
            display: none;
        }

        .filter-box .row {
            --bs-gutter-x: 1rem;
            --bs-gutter-y: 1rem;
        }

        .filter-shell {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 10px;
        }

        .filter-head {
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 2px 4px 0;
        }

        .filter-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--ink-900);
            font-size: 0.88rem;
            font-weight: 800;
        }

        .filter-kicker i {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), #0d9488);
        }

        .search-panel {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(150px, 0.62fr);
            gap: 8px;
            align-items: stretch;
        }

        .filter-field--dates {
            grid-column: 1 / -1;
            padding-left: 42px;
        }

        .date-range-inputs {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            align-items: center;
        }

        .date-range-inputs input {
            min-width: 0;
        }

        .filter-field {
            position: relative;
            display: grid;
            align-content: center;
            gap: 4px;
            min-height: 50px;
            border-radius: 14px;
            border: 1px solid #9fb8d1;
            background: #fff;
            padding: 9px 12px 9px 42px;
            transition: background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .filter-field:focus-within {
            background: #fff;
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(11, 111, 104, 0.16);
        }

        .filter-field--keyword {
            grid-column: 1 / -1;
            padding-left: 42px;
        }

        .filter-field__icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            display: grid;
            place-items: center;
            color: var(--brand);
            font-size: 0.9rem;
        }

        .filter-field .form-label-light {
            color: #17324f;
            font-size: 0.68rem;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
        }

        .filter-field .form-control,
        .filter-field .form-select {
            min-height: auto;
            height: 24px;
            border: 0;
            border-radius: 0;
            padding: 0;
            color: #071a2f;
            background-color: transparent;
            font-size: 0.9rem;
            font-weight: 800;
            box-shadow: none;
            opacity: 1;
        }

        .filter-field input[type="date"]::-webkit-datetime-edit,
        .filter-field input[type="date"]::-webkit-datetime-edit-fields-wrapper,
        .filter-field input[type="date"]::-webkit-datetime-edit-text,
        .filter-field input[type="date"]::-webkit-datetime-edit-month-field,
        .filter-field input[type="date"]::-webkit-datetime-edit-day-field,
        .filter-field input[type="date"]::-webkit-datetime-edit-year-field {
            color: #071a2f;
            font-weight: 800;
        }

        .filter-field input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0.72;
        }

        .filter-field .form-select {
            --bs-form-select-bg-img: none;
            appearance: auto;
        }

        .filter-submit {
            min-height: 50px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 12px 24px rgba(15, 118, 110, 0.26);
        }

        .filter-field .form-control::placeholder {
            color: #526b85;
            font-weight: 700;
            opacity: 1;
        }

        .filter-field .form-control:focus,
        .filter-field .form-select:focus {
            box-shadow: none;
        }

        .filter-command-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 10px;
            align-items: start;
        }

        .filter-secondary-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 10px;
            align-items: start;
        }

        .filter-field--compact {
            min-height: 46px;
            padding-top: 7px;
            padding-bottom: 7px;
        }

        .filter-field--compact .form-label-light {
            display: none;
        }

        .filter-field--compact .form-select {
            height: 28px;
        }

        .filter-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 0.74rem;
            font-weight: 700;
            background: #fff4de;
            border: 1px solid #f0bd6a;
            color: #7c3d00;
        }

        .filter-divider {
            margin: 10px 0 0;
            padding-top: 10px;
            border-top: 1px solid #d7e3ef;
        }

        .filter-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            justify-content: flex-start;
            gap: 10px;
        }

        .filter-actions .btn-soft {
            min-width: 0;
            min-height: 46px;
            padding: 8px 12px;
            border-color: #b8cbe0;
            color: var(--ink-700);
            background: #fff;
            font-size: 0.84rem;
            justify-content: center;
        }

        .advanced-filter {
            grid-column: 1 / -1;
            border-radius: 14px;
            border: 1px solid #9fb8d1;
            background: #f9fcff;
            overflow: hidden;
        }

        .advanced-filter[open] {
            background: #fff;
        }

        .advanced-filter__summary {
            list-style: none;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto auto;
            gap: 10px;
            align-items: center;
            cursor: pointer;
            min-height: 46px;
            padding: 9px 12px;
            background: linear-gradient(180deg, #ffffff, #f2f7fc);
        }

        .advanced-filter__summary::-webkit-details-marker {
            display: none;
        }

        .advanced-filter__title {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--ink-900);
            font-size: 0.88rem;
            font-weight: 800;
        }

        .advanced-filter__chevron {
            color: var(--ink-700);
            transition: transform 0.2s ease;
        }

        .advanced-filter[open] .advanced-filter__chevron {
            transform: rotate(180deg);
        }

        .advanced-filter__body {
            padding: 10px 12px 12px;
            border-top: 1px solid #d7e3ef;
        }

        .advanced-filter-grid {
            display: grid;
            grid-template-columns: minmax(190px, 1fr) minmax(240px, 1.15fr) minmax(190px, 1fr);
            gap: 10px;
        }

        .advanced-filter-card {
            border: 1px solid #c7d6e6;
            border-radius: 13px;
            background: #fff;
            padding: 9px 11px;
        }

        .advanced-filter-card--wide {
            grid-column: auto;
        }

        .advanced-filter-card .form-label-light {
            display: block;
            margin-bottom: 6px;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .advanced-filter-range {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .advanced-filter__body .form-label-light {
            color: #314862;
            font-weight: 800;
        }

        .advanced-filter__body .form-control,
        .advanced-filter__body .form-select {
            color: #071a2f;
            border-color: #c7d6e6;
            font-weight: 700;
            min-height: 40px;
            border-radius: 10px;
            font-size: 0.88rem;
        }

        .advanced-filter__body .form-control::placeholder {
            color: #526b85;
            font-weight: 700;
            opacity: 1;
        }

        .filter-check-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
        }

        .filter-check {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 40px;
            padding: 8px 11px;
            border-radius: 12px;
            border: 1px solid #b8cbe0;
            background: #fff;
            color: #10243e;
            font-weight: 700;
            transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        .filter-check .form-check-input {
            width: 17px;
            height: 17px;
            margin-top: 0;
            flex-shrink: 0;
            border: 2px solid #6f8ca8;
            background-color: #fff;
            box-shadow: none;
            opacity: 1;
            accent-color: var(--brand);
            cursor: pointer;
        }

        .filter-check .form-check-input:checked {
            border-color: var(--brand);
            background-color: var(--brand);
        }

        .filter-check:has(.form-check-input:checked) {
            border-color: var(--brand);
            background: #eefaf8;
            box-shadow: inset 0 0 0 1px rgba(15, 118, 110, 0.12);
        }

        .filter-check .form-check-label {
            color: #1f3853;
            font-size: 0.92rem;
            font-weight: 700;
        }

        .filter-summary {
            display: grid;
            gap: 8px;
            padding: 11px 13px;
            border-radius: 14px;
            border: 1px solid #dbe6f0;
            background: #f8fbff;
        }

        .filter-summary__label {
            color: var(--ink-700);
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .filter-summary__chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .filter-summary__chips .chip {
            padding: 6px 10px;
            font-size: 0.72rem;
            background: #e0f2fe;
            color: #155e75;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 1px solid var(--line);
            min-height: 44px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #6ab8b0;
            box-shadow: 0 0 0 0.2rem rgba(15, 118, 110, 0.16);
        }

        .btn-brand {
            border: none;
            border-radius: 12px;
            min-height: 48px;
            padding: 11px 16px;
            color: #fff;
            font-weight: 700;
            background: linear-gradient(135deg, var(--brand), #0d9488);
        }

        .btn-brand:hover {
            color: #fff;
            background: linear-gradient(135deg, var(--brand-dark), #0f766e);
        }

        .content-card {
            width: min(100%, 1320px);
            margin: 0 auto;
            border-radius: 20px;
            border: 1px solid #c7d6e6;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: var(--shadow-soft);
        }

        .content-card .btn-soft,
        .alert .btn-soft {
            border-color: #b8cbe0;
            color: var(--ink-900);
            background: #fff;
        }

        .content-card .btn-soft:hover,
        .alert .btn-soft:hover {
            color: var(--ink-900);
            border-color: #8fb4d5;
            background: #f4f9ff;
        }

        .section-panel {
            padding: clamp(20px, 1.8vw, 28px);
        }

        .room-section {
            border: 0;
            background: transparent;
            box-shadow: none;
            padding-inline: 0;
        }

        .room-section .section-head {
            padding-inline: 4px;
        }

        .room-section .section-count-pill {
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 10px 24px rgba(16, 42, 67, 0.07);
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 12px;
            margin-bottom: 18px;
        }

        .section-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 7px;
            color: var(--brand);
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .section-title {
            font-size: 1.55rem;
            margin: 0;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .section-subtitle {
            color: var(--ink-500);
            margin: 4px 0 0;
            max-width: 70ch;
            line-height: 1.7;
            font-weight: 600;
        }

        .section-count-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            border: 1px solid #b8cbe0;
            background: #f7fbff;
            color: var(--ink-700);
            padding: 8px 12px;
            font-size: 0.84rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 350px), 1fr));
            gap: 24px;
            align-items: stretch;
        }

        .room-card {
            position: relative;
            border: 1px solid rgba(197, 213, 229, 0.9);
            border-radius: 18px;
            padding: 0;
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfb 100%);
            display: flex;
            flex-direction: column;
            gap: 0;
            overflow: hidden;
            isolation: isolate;
            transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
            height: 100%;
            box-shadow: 0 18px 42px rgba(16, 42, 67, 0.09);
        }

        .room-card:hover {
            border-color: rgba(216, 168, 79, 0.62);
            box-shadow: 0 24px 52px rgba(16, 42, 67, 0.15);
            transform: translateY(-3px);
        }

        .room-card.active {
            border-color: #34a39a;
            box-shadow: 0 0 0 2px rgba(52, 163, 154, 0.22), 0 22px 48px rgba(16, 42, 67, 0.14);
            transform: translateY(-2px);
        }

        .room-gallery {
            position: relative;
            display: block;
            background: #0d2338;
        }

        .room-cover-wrap {
            position: relative;
            border-radius: 0;
            overflow: hidden;
            border: 0;
            background: linear-gradient(160deg, #ecf5ff, #f6fbff);
        }

        .room-cover-wrap::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(6, 18, 31, 0.02) 48%, rgba(6, 18, 31, 0.36) 100%);
            pointer-events: none;
        }

        .room-cover-trigger,
        .room-image-count,
        .room-thumb-trigger,
        .room-thumb-more {
            border: none;
            cursor: pointer;
        }

        .room-cover-trigger,
        .room-thumb-trigger {
            padding: 0;
            background: transparent;
        }

        .room-cover-trigger {
            width: 100%;
            display: block;
        }

        .room-cover,
        .room-cover--empty {
            width: 100%;
            height: 285px;
            display: block;
        }

        .room-cover {
            object-fit: cover;
            object-position: center;
            filter: saturate(1.04) contrast(1.02);
            transform: scale(1.01);
            transition: transform 0.35s ease, filter 0.35s ease;
        }

        .room-card:hover .room-cover {
            filter: saturate(1.08) contrast(1.04);
            transform: scale(1.045);
        }

        .room-cover--empty {
            display: grid;
            place-items: center;
            color: #5b728d;
            font-size: 2rem;
            background:
                radial-gradient(circle at top left, rgba(15, 118, 110, 0.16), transparent 30%),
                linear-gradient(160deg, #f4f8fc, #e9f1f8);
        }

        .room-image-count {
            position: absolute;
            right: 12px;
            top: 12px;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 10px;
            color: #fff;
            font-size: 0.76rem;
            font-weight: 700;
            background: rgba(8, 22, 37, 0.78);
            backdrop-filter: blur(4px);
        }

        .room-badges {
            position: absolute;
            left: 12px;
            top: 12px;
            right: 90px;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            pointer-events: none;
        }

        .room-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 10px;
            color: #fff;
            background: rgba(8, 22, 37, 0.76);
            backdrop-filter: blur(5px);
            font-size: 0.74rem;
            font-weight: 800;
        }

        .room-badge--success {
            background: rgba(15, 118, 110, 0.88);
        }

        .room-badge--gold {
            color: #261700;
            background: rgba(245, 223, 159, 0.92);
        }

        .room-thumbs {
            position: absolute;
            left: 14px;
            right: 14px;
            bottom: 14px;
            z-index: 2;
            display: flex;
            gap: 6px;
            padding: 0;
            pointer-events: none;
        }

        .room-thumb-trigger,
        .room-thumb-more {
            flex: 0 0 54px;
            width: 54px;
            height: 42px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.72);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.86);
            box-shadow: 0 8px 18px rgba(4, 18, 32, 0.22);
            pointer-events: auto;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .room-thumb-trigger:hover,
        .room-thumb-more:hover {
            border-color: #f4d58b;
            transform: translateY(-2px);
        }

        .room-cover-trigger:focus-visible,
        .room-image-count:focus-visible,
        .room-thumb-trigger:focus-visible,
        .room-thumb-more:focus-visible {
            outline: 3px solid rgba(15, 118, 110, 0.35);
            outline-offset: 2px;
        }

        .room-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .room-thumb-more {
            display: grid;
            place-items: center;
            color: #18324e;
            font-size: 0.82rem;
            font-weight: 800;
        }

        .room-gallery-hint {
            display: none;
        }

        .gallery-modal .modal-content {
            border: 0;
            border-radius: 24px;
            overflow: hidden;
            background: #0d1b2b;
            color: #fff;
            box-shadow: 0 28px 64px rgba(3, 10, 19, 0.42);
        }

        .gallery-modal .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 18px 22px;
        }

        .gallery-modal .modal-title {
            font-size: 1.15rem;
            font-weight: 700;
        }

        .gallery-modal .btn-close {
            filter: invert(1);
            opacity: 0.8;
        }

        .gallery-modal .btn-close:hover {
            opacity: 1;
        }

        .gallery-modal .modal-body {
            padding: 20px 22px 24px;
        }

        .gallery-stage {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 460px;
            border-radius: 20px;
            background:
                radial-gradient(circle at top, rgba(15, 118, 110, 0.22), transparent 36%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0.02));
        }

        .gallery-stage img {
            max-width: 100%;
            max-height: 70vh;
            object-fit: contain;
            border-radius: 16px;
        }

        .gallery-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 48px;
            height: 48px;
            border: 0;
            border-radius: 999px;
            display: grid;
            place-items: center;
            color: #fff;
            background: rgba(9, 19, 31, 0.72);
            backdrop-filter: blur(6px);
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .gallery-nav:hover {
            background: rgba(15, 118, 110, 0.9);
            transform: translateY(-50%) scale(1.04);
        }

        .gallery-nav:disabled {
            cursor: not-allowed;
            opacity: 0.45;
        }

        .gallery-nav--prev {
            left: 18px;
        }

        .gallery-nav--next {
            right: 18px;
        }

        .gallery-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 14px;
            color: #d6e3f1;
            font-size: 0.92rem;
        }

        .gallery-counter {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.08);
        }

        .gallery-thumbs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(88px, 1fr));
            gap: 10px;
            margin-top: 16px;
        }

        .gallery-thumb-button {
            padding: 0;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.04);
            opacity: 0.72;
            transition: opacity 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
        }

        .gallery-thumb-button:hover,
        .gallery-thumb-button.is-active {
            opacity: 1;
            transform: translateY(-2px);
            border-color: rgba(60, 213, 196, 0.72);
        }

        .gallery-thumb-button img {
            width: 100%;
            height: 76px;
            object-fit: cover;
            display: block;
        }

        .room-top {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            padding: 0;
        }

        .room-body {
            display: flex;
            flex: 1;
            flex-direction: column;
            gap: 14px;
            padding: 18px;
        }

        .tag {
            display: inline-flex;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 800;
            color: #79500f;
            background: #fff5dd;
            border: 1px solid #f2d89f;
        }

        .room-title {
            margin: 8px 0 0;
            color: #10243e;
            font-size: 1.22rem;
            line-height: 1.25;
            font-weight: 800;
        }

        .price {
            text-align: right;
            padding-top: 2px;
            font-size: 1.12rem;
            font-weight: 800;
            color: var(--brand);
            white-space: nowrap;
        }

        .price small {
            display: block;
            color: var(--ink-500);
            font-size: 0.75rem;
            font-weight: 700;
        }

        .room-meta {
            color: var(--ink-700);
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            font-size: 0.86rem;
            padding: 0;
        }

        .room-meta > div {
            min-height: 46px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #e4edf4;
            border-radius: 12px;
            background: #f8fbfa;
            padding: 8px 10px;
            line-height: 1.28;
            font-weight: 700;
        }

        .room-meta i {
            width: 18px;
            flex: 0 0 18px;
            text-align: center;
            color: #0f766e;
        }

        .room-description {
            margin: 0;
            border-left: 3px solid #d8a84f;
            border-radius: 12px;
            background: #fffaf0;
            color: #334e68;
            padding: 10px 12px;
            font-size: 0.86rem;
            line-height: 1.55;
            font-weight: 600;
        }

        .feature-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 0;
        }

        .feature-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 10px;
            border: 1px solid #dbece8;
            background: #f2faf7;
            color: #0f5f58;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .btn-choose {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: auto;
            border: 0;
            border-radius: 12px;
            min-height: 44px;
            padding: 11px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), #0d9488);
            transition: all 0.2s ease;
        }

        .btn-choose:hover {
            color: #fff;
            background: linear-gradient(135deg, var(--brand-dark), #0f766e);
            transform: translateY(-1px);
        }

        .selected-room {
            display: inline-flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            width: 100%;
            border-radius: 14px;
            padding: 12px 14px;
            background: #eefaf8;
            color: #0f5f58;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .booking-panel {
            display: grid;
            grid-template-columns: minmax(260px, 330px) minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        .booking-summary-card {
            border-radius: 18px;
            border: 1px solid #c7d6e6;
            background:
                linear-gradient(180deg, #ffffff, #f7fbff);
            padding: 16px;
            box-shadow: 0 12px 26px rgba(16, 42, 67, 0.07);
        }

        .booking-summary-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            color: var(--brand);
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .booking-summary-list {
            display: grid;
            gap: 10px;
            margin-top: 14px;
            color: var(--ink-700);
            font-size: 0.88rem;
            font-weight: 700;
        }

        .booking-summary-list div {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .booking-summary-list i {
            width: 28px;
            height: 28px;
            border-radius: 9px;
            display: grid;
            place-items: center;
            color: var(--brand);
            background: #eef8f6;
        }

        .booking-form-grid {
            margin: 0;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .chip-warning { background: #fef3c7; color: #92400e; }
        .chip-info { background: #dbeafe; color: #1d4ed8; }
        .chip-primary { background: #e0e7ff; color: #3730a3; }
        .chip-success { background: #dcfce7; color: #166534; }
        .chip-danger { background: #ffe4e6; color: #be123c; }
        .chip-neutral { background: #e2e8f0; color: #334155; }

        .alert {
            border-radius: 12px;
        }

        .muted-empty {
            border: 1px dashed #b8cbe0;
            border-radius: 14px;
            padding: 18px;
            color: var(--ink-500);
            background: #fbfdff;
            font-weight: 600;
        }

        .table-responsive {
            width: 100%;
            overflow: auto;
            border: 1px solid #e2eaf3;
            border-radius: 18px;
            background: #fff;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: #f6f9fc;
            color: #2b465f;
            border-bottom: 1px solid var(--line);
            font-size: 0.84rem;
            font-weight: 700;
            white-space: nowrap;
            padding: 14px 16px;
        }

        .table tbody td {
            border-bottom: 1px solid #eef3f8;
            vertical-align: middle;
            padding: 14px 16px;
        }

        .table tbody tr:hover {
            background: #fbfdff;
        }

        .site-footer {
            width: min(100%, 1320px);
            margin: 0 auto;
            border-radius: 22px;
            overflow: hidden;
            color: #e8f7ff;
            background:
                linear-gradient(135deg, rgba(7, 27, 58, 0.98), rgba(15, 118, 110, 0.94));
            box-shadow: 0 18px 42px rgba(7, 27, 58, 0.2);
        }

        .site-footer__main {
            display: grid;
            grid-template-columns: minmax(260px, 1.25fr) repeat(3, minmax(170px, 1fr));
            gap: 24px;
            padding: clamp(24px, 3vw, 34px);
        }

        .site-footer__brand {
            display: grid;
            gap: 12px;
        }

        .site-footer__brand-title {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            font-size: 1.05rem;
            font-weight: 800;
        }

        .site-footer__brand-title i {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            color: #08345f;
            background: #e8f7ff;
        }

        .site-footer p {
            margin: 0;
            max-width: 42ch;
            color: rgba(232, 247, 255, 0.78);
            font-size: 0.9rem;
            line-height: 1.7;
            font-weight: 600;
        }

        .site-footer__column {
            display: grid;
            align-content: start;
            gap: 10px;
        }

        .site-footer__column h3 {
            margin: 0;
            color: #fff;
            font-size: 0.9rem;
            font-weight: 800;
        }

        .site-footer__links,
        .site-footer__list {
            display: grid;
            gap: 8px;
        }

        .site-footer__links a,
        .site-footer__links button,
        .site-footer__list span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            border: 0;
            padding: 0;
            color: rgba(232, 247, 255, 0.82);
            background: transparent;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 700;
        }

        .site-footer__links a:hover,
        .site-footer__links button:hover {
            color: #fff;
        }

        .site-footer__links i,
        .site-footer__list i {
            width: 18px;
            color: #f5d58f;
            text-align: center;
        }

        .site-footer__bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.14);
            padding: 14px clamp(24px, 3vw, 34px);
            color: rgba(232, 247, 255, 0.76);
            font-size: 0.82rem;
            font-weight: 700;
        }

        .site-footer__badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border-radius: 999px;
            padding: 6px 10px;
            color: #08345f;
            background: #e8f7ff;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .text-muted,
        .small.text-muted,
        .text-muted.small,
        .section-subtitle,
        .muted-empty,
        .room-gallery-hint {
            color: #405a76 !important;
            font-weight: 600;
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.78) !important;
            font-weight: 600;
        }

        @media (min-width: 1400px) {
            .room-grid {
                grid-template-columns: repeat(auto-fit, minmax(390px, 1fr));
            }
        }

        @media (max-width: 1280px) {
            .search-panel {
                grid-template-columns: minmax(0, 1fr) minmax(260px, 1fr);
            }

            .filter-field--keyword {
                grid-column: 1 / -1;
            }

            .filter-submit {
                grid-column: span 1;
            }

            .filter-secondary-row {
                grid-template-columns: minmax(170px, 220px) auto;
            }
        }

        @media (max-width: 991px) {
            .booking-showcase {
                min-height: 520px;
                padding: 0;
                border-radius: 24px;
            }

            .quick-actions {
                left: 18px;
                bottom: 18px;
                width: min(390px, calc(100% - 36px));
            }

            .showcase-room-card,
            .showcase-room-image {
                min-height: 100%;
            }

            .showcase-room-copy {
                top: 24px;
                left: 22px;
                right: 22px;
                max-width: 680px;
            }

            .showcase-search-card {
                width: 100%;
                right: 12px;
                left: 12px;
                top: 78px;
                width: auto;
                max-height: calc(100vh - 94px);
            }

            .auth-panel {
                right: 12px;
                left: 12px;
                top: 78px;
                width: auto;
                max-height: calc(100vh - 94px);
            }

            .booking-panel {
                grid-template-columns: 1fr;
            }

            .search-strip {
                position: static;
            }

            .search-panel {
                grid-template-columns: 1fr;
            }

            .filter-command-row {
                grid-template-columns: 1fr;
            }

            .filter-secondary-row {
                grid-template-columns: 1fr;
            }

            .room-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .filter-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .filter-check-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .site-footer__main {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
                padding: 14px 16px;
            }

            .showcase-search-head {
                flex-direction: column;
            }

            .showcase-search-actions {
                width: 100%;
                justify-content: space-between;
            }

            .showcase-stats {
                grid-template-columns: 1fr;
            }

            .showcase-room-card,
            .showcase-room-image {
                min-height: 100%;
            }

            .booking-showcase {
                min-height: 460px;
                padding: 0;
                border-radius: 20px;
            }

            .quick-actions {
                left: 14px;
                right: 14px;
                bottom: 14px;
                width: auto;
                gap: 8px;
            }

            .quick-action {
                min-height: 62px;
                border-radius: 16px;
                padding: 10px 12px;
            }

            .quick-action:hover {
                transform: translateY(-3px);
            }

            .quick-action i {
                width: 40px;
                height: 40px;
                flex-basis: 40px;
                border-radius: 13px;
            }

            .quick-action small {
                font-size: 0.66rem;
            }

            .quick-action strong {
                font-size: 0.92rem;
            }

            .showcase-room-copy {
                top: 20px;
                left: 18px;
                right: 18px;
            }

            .showcase-room-copy h1 {
                font-size: 1.8rem;
            }

            .showcase-search-card {
                border-radius: 18px;
                padding: 12px;
                right: 10px;
                left: 10px;
                top: 74px;
            }

            .auth-panel {
                border-radius: 18px;
                padding: 14px;
                right: 10px;
                left: 10px;
                top: 74px;
            }

            .filter-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-panel {
                grid-template-columns: 1fr;
            }

            .filter-field {
                min-height: 62px;
            }

            .filter-submit {
                grid-column: auto;
                min-height: 58px;
            }

            .date-range-inputs {
                grid-template-columns: 1fr;
            }

            .room-grid {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }

            .room-cover,
            .room-cover--empty {
                height: 240px;
            }

            .room-body {
                padding: 15px;
            }

            .room-top {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .price {
                text-align: left;
            }

            .room-meta {
                grid-template-columns: 1fr;
            }

            .filter-box {
                padding: 0;
            }

            .advanced-filter__summary {
                grid-template-columns: minmax(0, 1fr) auto auto;
            }

            .filter-check-grid {
                grid-template-columns: 1fr;
            }

            .advanced-filter-grid,
            .advanced-filter-range {
                grid-template-columns: 1fr;
            }

            .filter-actions .btn-soft {
                width: 100%;
                min-width: 0;
            }

            .site-footer__main {
                grid-template-columns: 1fr;
            }

            .site-footer__bottom {
                flex-direction: column;
                align-items: flex-start;
            }

            .section-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .section-count-pill {
                white-space: normal;
            }

            .gallery-modal .modal-header,
            .gallery-modal .modal-body {
                padding-left: 16px;
                padding-right: 16px;
            }

            .gallery-stage {
                min-height: 300px;
            }

            .gallery-nav {
                width: 42px;
                height: 42px;
            }

            .gallery-nav--prev {
                left: 10px;
            }

            .gallery-nav--next {
                right: 10px;
            }

            .gallery-meta {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <main class="shell">
        <div class="topbar">
            <a href="{{ route('booking.index') }}" class="brand">
                <span class="brand-icon"><i class="fa-solid fa-hotel"></i></span>
                <span>
                    Quản lý khách sạn - Nhóm 4
                    <small class="d-block text-muted fw-semibold">Đặt phòng trực tuyến 24/7</small>
                </span>
            </a>

            <div class="top-actions">
                @auth
                    @if(auth()->user()->vai_tro === 'khach_hang')
                        <a href="{{ route('booking.account') }}" class="btn-soft">
                            <i class="fa-solid fa-id-card"></i>
                            Tài khoản của tôi
                        </a>
                        <a href="{{ route('booking.payments') }}" class="btn-soft">
                            <i class="fa-solid fa-credit-card"></i>
                            Thanh toán của tôi
                        </a>
                        <button type="button" class="top-search-trigger js-search-toggle" aria-controls="bookingSearchPanel" aria-expanded="false">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            Tìm phòng
                        </button>
                        <span class="fw-semibold text-muted">Xin chào, {{ auth()->user()->ho_ten }}</span>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn-soft">
                            <i class="fa-solid fa-chart-line"></i>
                            Vào trang quản trị
                        </a>
                        <button type="button" class="top-search-trigger js-search-toggle" aria-controls="bookingSearchPanel" aria-expanded="false">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            Tìm phòng
                        </button>
                    @endif

                    <a href="{{ route('logout') }}" class="btn-danger-soft">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Đăng xuất
                    </a>
                @else
                    <button type="button" class="btn-soft js-auth-toggle" data-auth-panel="login" aria-controls="loginPanel" aria-expanded="false">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Đăng nhập
                    </button>
                    <button type="button" class="top-search-trigger js-search-toggle" aria-controls="bookingSearchPanel" aria-expanded="false">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Tìm phòng
                    </button>
                    <button type="button" class="btn-soft js-auth-toggle" data-auth-panel="register" aria-controls="registerPanel" aria-expanded="false">
                        <i class="fa-solid fa-user-plus"></i>
                        Đăng ký
                    </button>
                @endauth
            </div>
        </div>

        <div class="search-backdrop js-search-close" aria-hidden="true"></div>

        @guest
            <section class="auth-panel" id="loginPanel" data-auth-panel="login" aria-hidden="true" aria-label="Đăng nhập">
                <div class="auth-panel__head">
                    <div>
                        <h2>Đăng nhập</h2>
                        <p>Truy cập tài khoản để đặt phòng và theo dõi đơn.</p>
                    </div>
                    <button type="button" class="auth-panel__close js-auth-close" aria-label="Đóng đăng nhập">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                @if($errors->any() && old('auth_modal') === 'login')
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <input type="hidden" name="auth_modal" value="login">

                    <div class="mb-3">
                        <label for="login_modal" class="form-label">Email hoặc tên đăng nhập</label>
                        <input id="login_modal" type="text" name="login" class="form-control" value="{{ old('auth_modal') === 'login' ? old('login') : '' }}" placeholder="Email hoặc tên đăng nhập" required>
                    </div>

                    <div class="mb-3">
                        <label for="password_modal" class="form-label">Mật khẩu</label>
                        <input id="password_modal" type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                    </div>

                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                        <label class="d-inline-flex align-items-center gap-2 fw-semibold text-muted">
                            <input type="checkbox" name="remember" value="1">
                            Ghi nhớ
                        </label>
                        <button type="button" class="auth-panel__link js-auth-toggle" data-auth-panel="forgot">Quên mật khẩu?</button>
                    </div>

                    <button type="submit" class="btn-brand w-100">
                        <i class="fa-solid fa-right-to-bracket me-1"></i>Đăng nhập
                    </button>
                </form>

                <div class="auth-panel__switch">
                    <span>Chưa có tài khoản?</span>
                    <button type="button" class="auth-panel__link js-auth-toggle" data-auth-panel="register">Đăng ký</button>
                </div>
            </section>

            <section class="auth-panel" id="registerPanel" data-auth-panel="register" aria-hidden="true" aria-label="Đăng ký">
                <div class="auth-panel__head">
                    <div>
                        <h2>Đăng ký</h2>
                        <p>Tạo tài khoản khách hàng để đặt phòng trực tuyến.</p>
                    </div>
                    <button type="button" class="auth-panel__close js-auth-close" aria-label="Đóng đăng ký">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                @if($errors->any() && old('auth_modal') === 'register')
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.post') }}">
                    @csrf
                    <input type="hidden" name="auth_modal" value="register">

                    <div class="mb-3">
                        <label for="register_name_modal" class="form-label">Họ tên</label>
                        <input id="register_name_modal" type="text" name="ho_ten" class="form-control" value="{{ old('auth_modal') === 'register' ? old('ho_ten') : '' }}" placeholder="Nguyễn Văn A" required>
                    </div>

                    <div class="mb-3">
                        <label for="register_username_modal" class="form-label">Tên đăng nhập</label>
                        <input id="register_username_modal" type="text" name="ten_dang_nhap" class="form-control" value="{{ old('auth_modal') === 'register' ? old('ten_dang_nhap') : '' }}" placeholder="ten_dang_nhap" required>
                    </div>

                    <div class="mb-3">
                        <label for="register_email_modal" class="form-label">Email</label>
                        <input id="register_email_modal" type="email" name="email" class="form-control" value="{{ old('auth_modal') === 'register' ? old('email') : '' }}" placeholder="ten@email.com" required>
                    </div>

                    <div class="mb-3">
                        <label for="register_phone_modal" class="form-label">Số điện thoại</label>
                        <input id="register_phone_modal" type="text" name="so_dien_thoai" class="form-control" value="{{ old('auth_modal') === 'register' ? old('so_dien_thoai') : '' }}" placeholder="0900000000">
                    </div>

                    <div class="mb-3">
                        <label for="register_password_modal" class="form-label">Mật khẩu</label>
                        <input id="register_password_modal" type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự" required>
                    </div>

                    <div class="mb-3">
                        <label for="register_password_confirmation_modal" class="form-label">Xác nhận mật khẩu</label>
                        <input id="register_password_confirmation_modal" type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu" required>
                    </div>

                    <button type="submit" class="btn-brand w-100">
                        <i class="fa-solid fa-user-plus me-1"></i>Đăng ký
                    </button>
                </form>

                <div class="auth-panel__switch">
                    <span>Đã có tài khoản?</span>
                    <button type="button" class="auth-panel__link js-auth-toggle" data-auth-panel="login">Đăng nhập</button>
                </div>
            </section>

            <section class="auth-panel" id="forgotPanel" data-auth-panel="forgot" aria-hidden="true" aria-label="Quên mật khẩu">
                <div class="auth-panel__head">
                    <div>
                        <h2>Quên mật khẩu</h2>
                        <p>Nhập email đã đăng ký để nhận liên kết đặt lại mật khẩu.</p>
                    </div>
                    <button type="button" class="auth-panel__close js-auth-close" aria-label="Đóng quên mật khẩu">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                @if(session('success') && session('auth_modal') === 'forgot')
                    <div class="alert alert-success mb-3">{{ session('success') }}</div>
                @endif

                @if($errors->any() && old('auth_modal') === 'forgot')
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <input type="hidden" name="auth_modal" value="forgot">

                    <div class="mb-3">
                        <label for="forgot_email_modal" class="form-label">Email tài khoản</label>
                        <input id="forgot_email_modal" type="email" name="email" class="form-control" value="{{ old('auth_modal') === 'forgot' ? old('email') : '' }}" placeholder="ten@email.com" required>
                        <div class="small text-muted mt-2">Hệ thống không thông báo email có tồn tại hay không.</div>
                    </div>

                    <button type="submit" class="btn-brand w-100">
                        <i class="fa-solid fa-paper-plane me-1"></i>Gửi liên kết đặt lại
                    </button>
                </form>

                <div class="auth-panel__switch">
                    <span>Nhớ mật khẩu?</span>
                    <button type="button" class="auth-panel__link js-auth-toggle" data-auth-panel="login">Đăng nhập</button>
                </div>
            </section>
        @endguest

        @php
            $phongNoiBat = collect($danhSachPhong->items())->first(fn ($phong) => $phong->layAnhPhongDauTienUrl());
            $anhHeroTuyChinh = 'uploads/hero/pexels-quang-nguyen-vinh-222549-14025909.jpg';
            $anhPhongNoiBat = file_exists(public_path($anhHeroTuyChinh))
                ? asset($anhHeroTuyChinh)
                : ($phongNoiBat?->layAnhPhongDauTienUrl()
                    ?: 'phong-105-20260424110253-x266vvk2');
            $tenPhongNoiBat = $phongNoiBat
                ? 'Phòng ' . $phongNoiBat->so_phong . ' - ' . ($phongNoiBat->loaiPhong->ten_loai_phong ?? 'Không gian nghỉ dưỡng')
                : 'Không gian phòng nghỉ cao cấp';
        @endphp

        <section class="booking-showcase">
            <article class="showcase-room-card">
                <img class="showcase-room-image" src="{{ $anhPhongNoiBat }}" alt="{{ $tenPhongNoiBat }}">
            </article>

        <nav class="quick-actions" aria-label="Lối tắt đặt phòng">
            @auth
                @if(auth()->user()->vai_tro === 'khach_hang')
                    <a href="{{ route('booking.account') }}" class="quick-action">
                        <i class="fa-solid fa-user-check"></i>
                        <span>
                            <small>Quản lý</small>
                            <strong>Tài khoản</strong>
                        </span>
                    </a>
                    <a href="{{ route('booking.payments') }}" class="quick-action">
                        <i class="fa-solid fa-credit-card"></i>
                        <span>
                            <small>Theo dõi</small>
                            <strong>Thanh toán</strong>
                        </span>
                    </a>
                    <a href="#booking-form" class="quick-action">
                        <i class="fa-solid fa-bell"></i>
                        <span>
                            <small>Tạo mới</small>
                            <strong>Đặt phòng</strong>
                        </span>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="quick-action">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>
                            <small>Nội bộ</small>
                            <strong>Trang quản trị</strong>
                        </span>
                    </a>
                    <a href="{{ route('dat-phong.index') }}" class="quick-action">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>
                            <small>Quản lý</small>
                            <strong>Đặt phòng</strong>
                        </span>
                    </a>
                @endif
            @else
                <a href="#booking-form" class="quick-action">
                    <i class="fa-solid fa-bed"></i>
                    <span>
                        <small>Khám phá</small>
                        <strong>Chọn phòng</strong>
                    </span>
                </a>
                <a href="{{ route('booking.index', ['sap_xep' => 'gia_tang']) }}" class="quick-action">
                    <i class="fa-solid fa-tags"></i>
                    <span>
                        <small>Ưu tiên</small>
                        <strong>Giá tốt</strong>
                    </span>
                </a>
                <a href="{{ route('booking.index', ['co_anh' => 1]) }}" class="quick-action">
                    <i class="fa-regular fa-images"></i>
                    <span>
                        <small>Bộ lọc</small>
                        <strong>Có hình ảnh</strong>
                    </span>
                </a>
            @endauth
        </nav>

            <section class="showcase-search-card" id="bookingSearchPanel" aria-hidden="true" aria-label="Bảng tìm kiếm phòng">
                <div class="showcase-search-head">
                    <div>
                        <h2>Tìm phòng phù hợp</h2>
                        <p>Lọc nhanh theo ngày ở, số khách và nhu cầu cơ bản.</p>
                    </div>
                    <div class="showcase-search-actions">
                        <span class="showcase-search-badge">
                            <i class="fa-solid fa-hotel"></i>
                            Khách sạn Việt Nam
                        </span>
                        <button type="button" class="search-panel-close js-search-close" aria-label="Đóng tìm kiếm">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <div class="showcase-stats" aria-label="Tổng quan phòng">
                    <div class="showcase-stat">
                        <strong>{{ $danhSachPhong->total() }}</strong>
                        <span>phòng phù hợp</span>
                    </div>
                    <div class="showcase-stat">
                        <strong>{{ count($danhSachLoaiPhong ?? []) }}</strong>
                        <span>loại phòng</span>
                    </div>
                    <div class="showcase-stat">
                        <strong>{{ ($soBoLocNangCao ?? 0) }}</strong>
                        <span>bộ lọc đang bật</span>
                    </div>
                </div>

                <section class="search-strip" aria-label="Tìm kiếm phòng">
                    <form method="GET" action="{{ route('booking.index') }}" class="filter-box">
                        <div class="filter-shell">
                            @if(($soBoLocNangCao ?? 0) > 0)
                                <div class="filter-head">
                                    <span class="filter-kicker">
                                        <i class="fa-solid fa-magnifying-glass-location"></i>
                                        Tìm phòng khách sạn
                                    </span>
                                    <span class="filter-status">
                                        <i class="fa-solid fa-sliders"></i>
                                        {{ $soBoLocNangCao }} bộ lọc nâng cao
                                    </span>
                                </div>
                            @else
                                <div class="filter-head">
                                    <span class="filter-kicker">
                                        <i class="fa-solid fa-magnifying-glass-location"></i>
                                        Tìm phòng khách sạn
                                    </span>
                                </div>
                            @endif

                            <div class="search-panel">
                                <div class="filter-field filter-field--keyword">
                                    <span class="filter-field__icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                                    <label for="tu_khoa" class="form-label-light">Từ khóa</label>
                                    <input
                                        type="text"
                                        id="tu_khoa"
                                        name="tu_khoa"
                                        class="form-control"
                                        value="{{ old('tu_khoa', $boLoc['tu_khoa'] ?? '') }}"
                                        placeholder="Số phòng, loại phòng, loại giường"
                                    >
                                </div>
                                <div class="filter-field filter-field--dates">
                                    <span class="filter-field__icon"><i class="fa-regular fa-calendar-check"></i></span>
                                    <label class="form-label-light">Lịch ở</label>
                                    <div class="date-range-inputs">
                                        <input
                                            type="date"
                                            id="ngay_nhan"
                                            name="ngay_nhan"
                                            class="form-control"
                                            min="{{ now()->toDateString() }}"
                                            value="{{ old('ngay_nhan', $boLoc['ngay_nhan'] ?? now()->toDateString()) }}"
                                            aria-label="Ngày nhận phòng"
                                        >
                                        <input
                                            type="date"
                                            id="ngay_tra"
                                            name="ngay_tra"
                                            class="form-control"
                                            min="{{ now()->toDateString() }}"
                                            value="{{ old('ngay_tra', $boLoc['ngay_tra'] ?? now()->toDateString()) }}"
                                            aria-label="Ngày trả phòng"
                                        >
                                    </div>
                                </div>
                                <div class="filter-field">
                                    <span class="filter-field__icon"><i class="fa-solid fa-users"></i></span>
                                    <label for="so_khach" class="form-label-light">Số khách</label>
                                    <input
                                        type="number"
                                        min="1"
                                        max="10"
                                        id="so_khach"
                                        name="so_khach"
                                        class="form-control"
                                        value="{{ old('so_khach', $boLoc['so_khach']) }}"
                                        placeholder="Ví dụ: 2"
                                    >
                                </div>
                                <button type="submit" class="btn-brand filter-submit">
                                    <i class="fa-solid fa-magnifying-glass"></i>Tìm ngay
                                </button>
                            </div>

                            <div class="filter-secondary-row">
                                <div class="filter-field filter-field--compact">
                                    <span class="filter-field__icon"><i class="fa-solid fa-arrow-down-wide-short"></i></span>
                                    <label for="sap_xep" class="form-label-light">Sắp xếp</label>
                                    <select id="sap_xep" name="sap_xep" class="form-select" aria-label="Sắp xếp phòng">
                                        @foreach($tuyChonSapXep as $giaTri => $nhanSapXep)
                                            <option value="{{ $giaTri }}" @selected(old('sap_xep', $boLoc['sap_xep'] ?? 'so_phong') === $giaTri)>{{ $nhanSapXep }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="filter-actions">
                                    <a href="{{ route('booking.index') }}" class="btn-soft">
                                        <i class="fa-solid fa-rotate-left me-1"></i>Xóa bộ lọc
                                    </a>
                                </div>

                                <details class="advanced-filter" @if($coMoBoLocNangCao ?? false) open @endif>
                                    <summary class="advanced-filter__summary">
                                        <span class="advanced-filter__title">
                                            <i class="fa-solid fa-sliders"></i>
                                            Lọc nâng cao
                                        </span>
                                        @if(($soBoLocNangCao ?? 0) > 0)
                                            <span class="filter-status">{{ $soBoLocNangCao }}</span>
                                        @endif
                                        <i class="fa-solid fa-chevron-down advanced-filter__chevron"></i>
                                    </summary>

                                    <div class="advanced-filter__body">
                                        <div class="advanced-filter-grid">
                                            <div class="advanced-filter-card advanced-filter-card--wide">
                                                <label for="loai_phong_id" class="form-label-light">Loại phòng</label>
                                                <select id="loai_phong_id" name="loai_phong_id" class="form-select">
                                                    <option value="">Tất cả</option>
                                                    @foreach($danhSachLoaiPhong as $loaiPhong)
                                                        <option value="{{ $loaiPhong->id }}" @selected((string) old('loai_phong_id', $boLoc['loai_phong_id'] ?? '') === (string) $loaiPhong->id)>
                                                            {{ $loaiPhong->ten_loai_phong }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="advanced-filter-card advanced-filter-card--wide">
                                                <label class="form-label-light">Khoảng giá</label>
                                                <div class="advanced-filter-range">
                                                    <input type="number" min="0" step="100000" id="gia_tu" name="gia_tu" class="form-control" value="{{ old('gia_tu', $boLoc['gia_tu'] ?? '') }}" placeholder="Từ 500000">
                                                    <input type="number" min="0" step="100000" id="gia_den" name="gia_den" class="form-control" value="{{ old('gia_den', $boLoc['gia_den'] ?? '') }}" placeholder="Đến 2000000">
                                                </div>
                                            </div>

                                            <div class="advanced-filter-card">
                                                <label for="loai_giuong" class="form-label-light">Loại giường</label>
                                                <select id="loai_giuong" name="loai_giuong" class="form-select">
                                                    <option value="">Tất cả</option>
                                                    @foreach($danhSachLoaiGiuong as $loaiGiuong)
                                                        <option value="{{ $loaiGiuong }}" @selected(old('loai_giuong', $boLoc['loai_giuong'] ?? '') === $loaiGiuong)>
                                                            {{ $loaiGiuong }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="filter-divider">
                                            <div class="filter-check-grid">
                                                @foreach($tuyChonTienNghi as $tenTruong => $nhanTienNghi)
                                                    <label class="filter-check" for="{{ $tenTruong }}">
                                                        <input class="form-check-input" type="checkbox" value="1" id="{{ $tenTruong }}" name="{{ $tenTruong }}" @checked(old($tenTruong, $boLoc[$tenTruong] ?? false))>
                                                        <span class="form-check-label">{{ $nhanTienNghi }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </details>
                            </div>

                            @if(!empty($tomTatBoLoc) || ($boLoc['co_anh'] ?? false))
                                @php
                                    $tomTatHienThi = collect($tomTatBoLoc);

                                    if (($boLoc['co_anh'] ?? false) && !$tomTatHienThi->contains('Có ảnh')) {
                                        $tomTatHienThi->push('Có ảnh');
                                    }
                                @endphp
                                <div class="filter-summary">
                                    <div class="filter-summary__label">Bộ lọc đang áp dụng</div>
                                    <div class="filter-summary__chips">
                                        @foreach($tomTatHienThi->unique() as $mucTomTat)
                                            <span class="chip">{{ $mucTomTat }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                </section>
            </section>
        </section>



        @if(session('success') && !session('auth_modal'))
            <div class="alert alert-success js-auto-dismiss-alert" data-auto-dismiss="5000">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger js-auto-dismiss-alert" data-auto-dismiss="5000">{{ session('error') }}</div>
        @endif

        @if($errors->any() && !old('auth_modal'))
            <div class="alert alert-danger js-auto-dismiss-alert" data-auto-dismiss="5000">
                <div class="fw-semibold mb-1">Vui lòng kiểm tra lại thông tin:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="content-card section-panel room-section">
            <div class="section-head">
                <div>
                    <span class="section-eyebrow">
                        <i class="fa-solid fa-bed"></i>
                        Chọn phòng
                    </span>
                    <h2 class="section-title">Danh sách phòng khả dụng</h2>
                    <p class="section-subtitle">Hiện có {{ $danhSachPhong->total() }} phòng phù hợp với bộ lọc.</p>
                </div>
                <span class="section-count-pill">
                    <i class="fa-solid fa-layer-group"></i>
                    {{ $danhSachPhong->count() }} phòng đang hiển thị
                </span>
            </div>

            @if($danhSachPhong->isEmpty())
                <div class="muted-empty">
                    Không có phòng phù hợp với bộ lọc hiện tại.
                </div>
            @else
                <div class="room-grid">
                    @foreach($danhSachPhong as $phong)
                        @php
                            $giaPhong = (float) ($phong->gia_mac_dinh ?? $phong->loaiPhong->gia_mot_dem ?? 0);
                            $dangDuocChon = (int) old('phong_id') === $phong->id;
                            $tenLoaiPhong = $phong->loaiPhong->ten_loai_phong ?? 'Loại phòng chưa cập nhật';
                            $danhSachAnhPhong = $phong->layDanhSachAnhPhong();
                            $danhSachAnhPhongUrl = array_map(fn ($anhPhong) => asset($anhPhong), $danhSachAnhPhong);
                            $anhPhongDauTien = $phong->layAnhPhongDauTienUrl();
                            $tongSoAnhPhong = count($danhSachAnhPhong);
                        @endphp

                        <article
                            class="room-card {{ $dangDuocChon ? 'active' : '' }}"
                            data-room-id="{{ $phong->id }}"
                            data-room-name="Phòng {{ $phong->so_phong }} - {{ $tenLoaiPhong }}"
                            data-room-images='@json($danhSachAnhPhongUrl)'
                        >
                            <div class="room-gallery">
                                <div class="room-cover-wrap">
                                    @if($anhPhongDauTien)
                                        <button
                                            type="button"
                                            class="room-cover-trigger js-open-gallery"
                                            data-room-id="{{ $phong->id }}"
                                            data-image-index="0"
                                            aria-label="Xem ảnh lớn của phòng {{ $phong->so_phong }}"
                                        >
                                            <img class="room-cover" src="{{ $anhPhongDauTien }}" alt="Ảnh phòng {{ $phong->so_phong }}">
                                        </button>
                                    @else
                                        <div class="room-cover--empty">
                                            <i class="fa-regular fa-image"></i>
                                        </div>
                                    @endif

                                    @if($tongSoAnhPhong > 0)
                                        <button
                                            type="button"
                                            class="room-image-count js-open-gallery"
                                            data-room-id="{{ $phong->id }}"
                                            data-image-index="0"
                                            aria-label="Xem toàn bộ ảnh của phòng {{ $phong->so_phong }}"
                                        >
                                            <i class="fa-regular fa-images"></i>{{ $tongSoAnhPhong }} ảnh
                                        </button>
                                    @endif

                                    <div class="room-badges">
                                        <span class="room-badge room-badge--success">
                                            <i class="fa-solid fa-check"></i>Còn trống
                                        </span>
                                        @if($phong->loaiPhong?->co_huong_bien)
                                            <span class="room-badge room-badge--gold">
                                                <i class="fa-solid fa-water"></i>Hướng biển
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($tongSoAnhPhong > 1)
                                    <div class="room-thumbs">
                                        @foreach(array_slice($danhSachAnhPhong, 1, 3) as $viTri => $anhPhongPhu)
                                            <button
                                                type="button"
                                                class="room-thumb-trigger js-open-gallery"
                                                data-room-id="{{ $phong->id }}"
                                                data-image-index="{{ $viTri + 1 }}"
                                                aria-label="Xem ảnh {{ $viTri + 2 }} của phòng {{ $phong->so_phong }}"
                                            >
                                                <img class="room-thumb" src="{{ asset($anhPhongPhu) }}" alt="Ảnh phụ phòng {{ $phong->so_phong }}">
                                            </button>
                                        @endforeach

                                        @if($tongSoAnhPhong > 4)
                                            <button
                                                type="button"
                                                class="room-thumb-more js-open-gallery"
                                                data-room-id="{{ $phong->id }}"
                                                data-image-index="0"
                                                aria-label="Xem thêm ảnh của phòng {{ $phong->so_phong }}"
                                            >
                                                +{{ $tongSoAnhPhong - 4 }}
                                            </button>
                                        @endif
                                    </div>
                                @endif

                            </div>

                            <div class="room-body">
                                <div class="room-top">
                                    <div>
                                        <span class="tag">Phòng {{ $phong->so_phong }}</span>
                                        <h3 class="room-title">{{ $tenLoaiPhong }}</h3>
                                    </div>
                                    <div class="price">
                                        {{ number_format($giaPhong, 0, ',', '.') }}
                                        <small>VNĐ / đêm</small>
                                    </div>
                                </div>

                                <div class="room-meta">
                                    <div><i class="fa-solid fa-users"></i>Tối đa {{ $phong->loaiPhong->so_nguoi_toi_da ?? 1 }} khách</div>
                                    <div><i class="fa-solid fa-bed"></i>{{ $phong->loaiPhong->so_giuong ?? 1 }} giường - {{ $phong->loaiPhong->loai_giuong ?? 'Tiêu chuẩn' }}</div>
                                    @if($phong->loaiPhong?->dien_tich)
                                        <div><i class="fa-solid fa-ruler-combined"></i>{{ number_format((float) $phong->loaiPhong->dien_tich, 0, ',', '.') }} m²</div>
                                    @endif
                                    <div><i class="fa-solid fa-bath"></i>{{ $phong->loaiPhong->so_phong_tam ?? 1 }} phòng tắm</div>
                                    @if($phong->tang)
                                        <div><i class="fa-solid fa-building"></i>Tầng {{ $phong->tang }}</div>
                                    @endif
                                </div>

                                @if(!empty($phong->loaiPhong->mo_ta))
                                    <p class="room-description">{{ $phong->loaiPhong->mo_ta }}</p>
                                @endif

                                @if($phong->loaiPhong?->co_ban_cong || $phong->loaiPhong?->co_bep_rieng || $phong->loaiPhong?->co_huong_bien)
                                    <div class="feature-pills">
                                        @if($phong->loaiPhong?->co_ban_cong)
                                            <span class="feature-pill"><i class="fa-solid fa-door-open"></i>Ban công</span>
                                        @endif
                                        @if($phong->loaiPhong?->co_bep_rieng)
                                            <span class="feature-pill"><i class="fa-solid fa-kitchen-set"></i>Bếp riêng</span>
                                        @endif
                                        @if($phong->loaiPhong?->co_huong_bien)
                                            <span class="feature-pill"><i class="fa-solid fa-water"></i>Hướng biển</span>
                                        @endif
                                    </div>
                                @endif

                                <button type="button" class="btn-choose js-choose-room" data-room-id="{{ $phong->id }}">
                                    <i class="fa-solid fa-check me-1"></i>Chọn phòng này
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-3">
                    {{ $danhSachPhong->links() }}
                </div>
            @endif
        </section>

        <section id="booking-form" class="content-card section-panel">
            <div class="section-head">
                <div>
                    <span class="section-eyebrow">
                        <i class="fa-solid fa-paper-plane"></i>
                        Hoàn tất đặt phòng
                    </span>
                    <h2 class="section-title">Gửi yêu cầu đặt phòng</h2>
                    <p class="section-subtitle">Chúng tôi sẽ liên hệ xác nhận.</p>
                </div>
            </div>

            @guest
                <div class="alert alert-warning mb-0">
                    Bạn vui lòng đăng nhập để đặt phòng trực tuyến.
                    <div class="d-flex gap-2 flex-wrap mt-2">
                        <button type="button" class="btn-soft js-auth-toggle" data-auth-panel="login">Đăng nhập</button>
                        <button type="button" class="btn-soft js-auth-toggle" data-auth-panel="register">Đăng ký</button>
                    </div>
                </div>
            @else
                @if(auth()->user()->vai_tro !== 'khach_hang')
                    <div class="alert alert-warning mb-0">
                        Tài khoản hiện tại là tài khoản nội bộ. Vui lòng dùng tài khoản khách hàng để đặt phòng online.
                    </div>
                @else
                    <div class="booking-panel">
                        <aside class="booking-summary-card">
                            <span class="booking-summary-kicker">
                                <i class="fa-solid fa-receipt"></i>
                                Tóm tắt yêu cầu
                            </span>
                            <div class="selected-room" id="selected-room-label">
                                <i class="fa-solid fa-door-open"></i>
                                <span>
                                    {{ old('phong_id') ? 'Phòng đã chọn: ID ' . old('phong_id') : 'Bạn chưa chọn phòng. Hãy chọn phòng ở danh sách phía trên.' }}
                                </span>
                            </div>
                            <div class="booking-summary-list">
                                <div><i class="fa-regular fa-calendar-check"></i>Ngày ở lấy theo form bên cạnh</div>
                                <div><i class="fa-solid fa-shield-heart"></i>Yêu cầu được nhân viên xác nhận</div>
                                <div><i class="fa-solid fa-credit-card"></i>Thanh toán sau khi đơn được duyệt</div>
                            </div>
                        </aside>

                        <form method="POST" action="{{ route('booking.store') }}" class="booking-form-grid row g-3">
                            @csrf
                            <input type="hidden" id="phong_id_input" name="phong_id" value="{{ old('phong_id') }}">

                        <div class="col-lg-4">
                            <label class="form-label">Họ tên</label>
                            <input type="text" class="form-control" name="ho_ten" value="{{ old('ho_ten', auth()->user()->ho_ten) }}" required>
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" name="so_dien_thoai" value="{{ old('so_dien_thoai', auth()->user()->so_dien_thoai) }}">
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email', auth()->user()->email) }}">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Ngày nhận phòng</label>
                            <input
                                type="date"
                                class="form-control"
                                name="ngay_nhan"
                                min="{{ now()->toDateString() }}"
                                value="{{ old('ngay_nhan', $boLoc['ngay_nhan'] ?? now()->toDateString()) }}"
                                required
                            >
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Ngày trả phòng</label>
                            <input
                                type="date"
                                class="form-control"
                                name="ngay_tra"
                                min="{{ now()->toDateString() }}"
                                value="{{ old('ngay_tra', $boLoc['ngay_tra'] ?? now()->toDateString()) }}"
                                required
                            >
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Số người lớn</label>
                            <input type="number" min="1" max="10" class="form-control" name="so_nguoi_lon" value="{{ old('so_nguoi_lon', 1) }}" required>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Số trẻ em</label>
                            <input type="number" min="0" max="10" class="form-control" name="so_tre_em" value="{{ old('so_tre_em', 0) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Yêu cầu đặc biệt</label>
                            <textarea class="form-control" rows="3" name="yeu_cau_dac_biet" placeholder="Ví dụ: phòng tầng cao, nhận phòng muộn, cần nôi em bé...">{{ old('yeu_cau_dac_biet') }}</textarea>
                        </div>

                            <div class="col-12">
                                <button type="submit" class="btn-brand">
                                    <i class="fa-solid fa-paper-plane me-1"></i>Gửi yêu cầu đặt phòng
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            @endguest
        </section>

        @auth
            @if(auth()->user()->vai_tro === 'khach_hang')
                <section class="content-card section-panel">
                    <div class="section-head">
                        <div>
                            <h2 class="section-title">Đơn đặt phòng của tôi</h2>
                            <p class="section-subtitle">Đơn và thanh toán gần đây.</p>
                        </div>
                        <a href="{{ route('booking.account') }}" class="btn-soft">
                            <i class="fa-solid fa-user"></i>
                            Mở trang khách hàng
                        </a>
                    </div>

                    @if($danhSachDonCuaToi->isEmpty())
                        <div class="muted-empty">Bạn chưa có đơn đặt phòng nào.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Phòng</th>
                                        <th>Lịch ở</th>
                                        <th>Trạng thái đơn</th>
                                        <th>Hóa đơn</th>
                                        <th>Đã thu</th>
                                        <th>Còn lại</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($danhSachDonCuaToi as $don)
                                        @php
                                            $phong = $don->chiTietDatPhong->first()?->phong;
                                            $hoaDon = $don->hoaDon->where('trang_thai', '!=', 'da_huy')->first();
                                            $tongTien = (float) ($hoaDon->tong_tien ?? 0);
                                            $soTienDaThu = $hoaDon ? (float) $hoaDon->thanhToan->where('trang_thai', 'thanh_cong')->sum('so_tien') : 0;
                                            $soTienConLai = max(0, $tongTien - $soTienDaThu);

                                            $mapTrangThaiDon = [
                                                'cho_xac_nhan' => 'chip-warning',
                                                'da_xac_nhan' => 'chip-info',
                                                'da_nhan_phong' => 'chip-primary',
                                                'khong_den' => 'chip-danger',
                                                'da_tra_phong' => 'chip-success',
                                                'da_huy' => 'chip-danger',
                                            ];
                                            $chipDon = $mapTrangThaiDon[$don->trang_thai] ?? 'chip-neutral';
                                            $lopCanhBaoTuDong = $don->da_qua_han_tu_dong_xu_ly
                                                ? 'text-danger'
                                                : ($don->sap_tu_dong_xu_ly ? 'text-warning' : 'text-muted');
                                        @endphp
                                        <tr>
                                            <td class="fw-bold">{{ $don->ma_dat_phong }}</td>
                                            <td>{{ $phong ? 'Phòng ' . $phong->so_phong : '-' }}</td>
                                            <td>
                                                {{ optional($don->ngay_nhan_phong_du_kien)->format('d/m/Y') }}
                                                -
                                                {{ optional($don->ngay_tra_phong_du_kien)->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                <span class="chip {{ $chipDon }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($don->trang_thai) }}</span>
                                                @if($don->co_tu_dong_xu_ly_khach_dat)
                                                    <div class="small mt-2 {{ $lopCanhBaoTuDong }}">
                                                        {{ $don->hanh_dong_tu_dong_xu_ly }} lúc {{ optional($don->han_tu_dong_xu_ly)->format('H:i d/m/Y') }}
                                                    </div>
                                                    <div class="small {{ $lopCanhBaoTuDong }}">
                                                        {{ $don->mo_ta_thoi_gian_tu_dong_xu_ly }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($hoaDon)
                                                    {{ $hoaDon->ma_hoa_don }}
                                                    <div class="small text-muted">{{ \App\Support\HienThiGiaTri::nhanGiaTri($hoaDon->trang_thai) }}</div>
                                                    @php
                                                        $soTienChoXuLy = (float) $hoaDon->thanhToan->where('trang_thai', 'cho_xu_ly')->sum('so_tien');
                                                    @endphp
                                                    @if($soTienChoXuLy > 0)
                                                        <div class="small text-warning">Đang chờ đối soát: {{ number_format($soTienChoXuLy, 0, ',', '.') }} VNĐ</div>
                                                    @endif
                                                    <div class="mt-2">
                                                        <a href="{{ route('booking.hoa-don.show', $hoaDon) }}" class="btn btn-sm btn-outline-secondary rounded-3">
                                                            {{ $soTienConLai > 0 || $soTienChoXuLy > 0 ? 'Xem thanh toán' : 'Xem chi tiết' }}
                                                        </a>
                                                    </div>
                                                @else
                                                    Chưa lập
                                                @endif
                                            </td>
                                            <td class="fw-semibold text-success">{{ number_format($soTienDaThu, 0, ',', '.') }} VNĐ</td>
                                            <td class="fw-semibold {{ $soTienConLai > 0 ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($soTienConLai, 0, ',', '.') }} VNĐ
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            @endif
        @endauth

        <div class="modal fade gallery-modal" id="roomGalleryModal" tabindex="-1" aria-labelledby="roomGalleryTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h3 class="modal-title" id="roomGalleryTitle">Ảnh phòng</h3>
                            <div class="small text-white-50 mt-1" id="roomGallerySubtitle">Xem chi tiết hình ảnh phòng trước khi đặt.</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="gallery-stage">
                            <button type="button" class="gallery-nav gallery-nav--prev" id="roomGalleryPrev" aria-label="Ảnh trước">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            <img id="roomGalleryImage" src="" alt="Ảnh phòng">
                            <button type="button" class="gallery-nav gallery-nav--next" id="roomGalleryNext" aria-label="Ảnh tiếp theo">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>

                        <div class="gallery-meta">
                            <div class="gallery-counter" id="roomGalleryCounter">
                                <i class="fa-regular fa-images"></i>
                                <span>1 / 1</span>
                            </div>
                            <div id="roomGalleryCaption">Nhấn vào ảnh nhỏ để chuyển nhanh giữa các góc chụp.</div>
                        </div>

                        <div class="gallery-thumbs" id="roomGalleryThumbs"></div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="site-footer" aria-label="Thông tin cuối trang">
            <div class="site-footer__main">
                <div class="site-footer__brand">
                    <div class="site-footer__brand-title">
                        <i class="fa-solid fa-hotel"></i>
                        <span>Quản lý khách sạn - Nhóm 4</span>
                    </div>
                    <p>Hệ thống đặt phòng trực tuyến hỗ trợ khách hàng xem phòng, lọc nhu cầu và gửi yêu cầu đặt phòng nhanh chóng.</p>
                </div>

                <div class="site-footer__column">
                    <h3>Điều hướng</h3>
                    <div class="site-footer__links">
                        <a href="{{ route('booking.index') }}"><i class="fa-solid fa-house"></i>Trang chủ</a>
                        <a href="#booking-form"><i class="fa-solid fa-paper-plane"></i>Gửi yêu cầu</a>
                        <a href="{{ route('booking.index', ['co_anh' => 1]) }}"><i class="fa-regular fa-images"></i>Phòng có ảnh</a>
                    </div>
                </div>

                <div class="site-footer__column">
                    <h3>Tài khoản</h3>
                    <div class="site-footer__links">
                        @auth
                            @if(auth()->user()->vai_tro === 'khach_hang')
                                <a href="{{ route('booking.account') }}"><i class="fa-solid fa-id-card"></i>Tài khoản của tôi</a>
                                <a href="{{ route('booking.payments') }}"><i class="fa-solid fa-credit-card"></i>Thanh toán</a>
                            @else
                                <a href="{{ route('dashboard') }}"><i class="fa-solid fa-chart-line"></i>Trang quản trị</a>
                            @endif
                            <a href="{{ route('logout') }}"><i class="fa-solid fa-right-from-bracket"></i>Đăng xuất</a>
                        @else
                            <button type="button" class="js-auth-toggle" data-auth-panel="login"><i class="fa-solid fa-right-to-bracket"></i>Đăng nhập</button>
                            <button type="button" class="js-auth-toggle" data-auth-panel="register"><i class="fa-solid fa-user-plus"></i>Đăng ký</button>
                        @endauth
                    </div>
                </div>

                <div class="site-footer__column">
                    <h3>Liên hệ</h3>
                    <div class="site-footer__list">
                        <span><i class="fa-solid fa-phone"></i>0900 000 000</span>
                        <span><i class="fa-solid fa-envelope"></i>booking@nhom4.hotel</span>
                        <span><i class="fa-solid fa-clock"></i>Hỗ trợ 24/7</span>
                    </div>
                </div>
            </div>

            <div class="site-footer__bottom">
                <span>© {{ now()->year }} Quản lý khách sạn - Nhóm 4. Nền tảng đặt phòng trực tuyến.</span>
                <span class="site-footer__badge"><i class="fa-solid fa-shield-heart"></i>Laravel + Apache</span>
            </div>
        </footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @include('partials.auto-dismiss-alerts')
    <script>
        const roomCards = Array.from(document.querySelectorAll('.room-card[data-room-id]'));
        const roomButtons = Array.from(document.querySelectorAll('.js-choose-room'));
        const galleryTriggers = Array.from(document.querySelectorAll('.js-open-gallery'));
        const roomInput = document.getElementById('phong_id_input');
        const selectedRoomText = document.querySelector('#selected-room-label span');
        const roomGalleryModalElement = document.getElementById('roomGalleryModal');
        const roomGalleryTitle = document.getElementById('roomGalleryTitle');
        const roomGallerySubtitle = document.getElementById('roomGallerySubtitle');
        const roomGalleryImage = document.getElementById('roomGalleryImage');
        const roomGalleryCounter = document.querySelector('#roomGalleryCounter span');
        const roomGalleryCaption = document.getElementById('roomGalleryCaption');
        const roomGalleryThumbs = document.getElementById('roomGalleryThumbs');
        const roomGalleryPrev = document.getElementById('roomGalleryPrev');
        const roomGalleryNext = document.getElementById('roomGalleryNext');
        const roomGalleryModal = roomGalleryModalElement ? new bootstrap.Modal(roomGalleryModalElement) : null;
        const searchTriggers = Array.from(document.querySelectorAll('.js-search-toggle'));
        const searchCloseButtons = Array.from(document.querySelectorAll('.js-search-close'));
        const searchPanel = document.getElementById('bookingSearchPanel');
        const firstSearchInput = searchPanel?.querySelector('#tu_khoa, input, select, button');
        const authTriggers = Array.from(document.querySelectorAll('.js-auth-toggle'));
        const authCloseButtons = Array.from(document.querySelectorAll('.js-auth-close'));
        const authPanels = Array.from(document.querySelectorAll('.auth-panel[data-auth-panel]'));
        const initialAuthPanel = @json(old('auth_modal') ?: session('auth_modal'));

        let activeGalleryImages = [];
        let activeGalleryIndex = 0;
        let activeGalleryRoomName = '';

        function setSearchPanelOpen(isOpen) {
            if (isOpen) {
                setAuthPanelOpen(null);
            }

            document.body.classList.toggle('search-panel-open', isOpen);
            searchPanel?.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            searchTriggers.forEach((button) => {
                button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            if (isOpen) {
                window.setTimeout(() => firstSearchInput?.focus(), 120);
            }
        }

        function setAuthPanelOpen(panelName) {
            const isOpen = Boolean(panelName);

            if (isOpen) {
                setSearchPanelOpen(false);
            }

            document.body.classList.toggle('auth-panel-open', isOpen);

            authPanels.forEach((panel) => {
                const active = panel.dataset.authPanel === panelName;
                panel.classList.toggle('is-active', active);
                panel.setAttribute('aria-hidden', active ? 'false' : 'true');
            });

            authTriggers.forEach((button) => {
                const active = button.dataset.authPanel === panelName;
                button.setAttribute('aria-expanded', active ? 'true' : 'false');
            });

            if (isOpen) {
                const activePanel = authPanels.find((panel) => panel.dataset.authPanel === panelName);
                const firstInput = activePanel?.querySelector('input:not([type="hidden"]), select, button[type="submit"]');
                window.setTimeout(() => firstInput?.focus(), 120);
            }
        }

        function closeFloatingPanels() {
            setSearchPanelOpen(false);
            setAuthPanelOpen(null);
        }

        searchTriggers.forEach((button) => {
            button.addEventListener('click', () => {
                setSearchPanelOpen(!document.body.classList.contains('search-panel-open'));
            });
        });

        searchCloseButtons.forEach((button) => {
            button.addEventListener('click', closeFloatingPanels);
        });

        authTriggers.forEach((button) => {
            button.addEventListener('click', () => {
                const panelName = button.dataset.authPanel;
                const isActive = document.body.classList.contains('auth-panel-open')
                    && authPanels.some((panel) => panel.dataset.authPanel === panelName && panel.classList.contains('is-active'));

                setAuthPanelOpen(isActive ? null : panelName);
            });
        });

        authCloseButtons.forEach((button) => {
            button.addEventListener('click', () => setAuthPanelOpen(null));
        });

        function setSelectedRoom(roomId) {
            const roomCard = roomCards.find((item) => String(item.dataset.roomId) === String(roomId));
            roomCards.forEach((item) => item.classList.remove('active'));

            if (!roomCard) {
                if (roomInput) {
                    roomInput.value = '';
                }

                if (selectedRoomText) {
                    selectedRoomText.textContent = 'Bạn chưa chọn phòng. Hãy chọn phòng ở danh sách phía trên.';
                }
                return;
            }

            roomCard.classList.add('active');

            if (roomInput) {
                roomInput.value = roomId;
            }

            if (selectedRoomText) {
                selectedRoomText.textContent = 'Phòng đã chọn: ' + roomCard.dataset.roomName;
            }
        }

        function layDanhSachAnhPhong(roomCard) {
            if (!roomCard?.dataset.roomImages) {
                return [];
            }

            try {
                const danhSach = JSON.parse(roomCard.dataset.roomImages);
                return Array.isArray(danhSach) ? danhSach : [];
            } catch (error) {
                return [];
            }
        }

        function capNhatModalAnhPhong() {
            if (!activeGalleryImages.length || !roomGalleryImage) {
                return;
            }

            const tongSoAnh = activeGalleryImages.length;
            const anhHienTai = activeGalleryImages[activeGalleryIndex];

            roomGalleryImage.src = anhHienTai;
            roomGalleryImage.alt = activeGalleryRoomName + ' - ảnh ' + (activeGalleryIndex + 1);

            if (roomGalleryTitle) {
                roomGalleryTitle.textContent = activeGalleryRoomName || 'Ảnh phòng';
            }

            if (roomGallerySubtitle) {
                roomGallerySubtitle.textContent = tongSoAnh > 1
                    ? 'Dùng mũi tên hoặc ảnh nhỏ bên dưới để xem các góc chụp khác.'
                    : 'Phòng này hiện có 1 ảnh được đăng.';
            }

            if (roomGalleryCounter) {
                roomGalleryCounter.textContent = (activeGalleryIndex + 1) + ' / ' + tongSoAnh;
            }

            if (roomGalleryCaption) {
                roomGalleryCaption.textContent = 'Ảnh ' + (activeGalleryIndex + 1) + ' trong bộ ảnh của ' + activeGalleryRoomName + '.';
            }

            if (roomGalleryPrev) {
                roomGalleryPrev.disabled = tongSoAnh <= 1;
            }

            if (roomGalleryNext) {
                roomGalleryNext.disabled = tongSoAnh <= 1;
            }

            if (roomGalleryThumbs) {
                roomGalleryThumbs.innerHTML = '';

                activeGalleryImages.forEach((anh, index) => {
                    const nutAnh = document.createElement('button');
                    nutAnh.type = 'button';
                    nutAnh.className = 'gallery-thumb-button' + (index === activeGalleryIndex ? ' is-active' : '');
                    nutAnh.setAttribute('aria-label', 'Xem ảnh ' + (index + 1));

                    const hinhAnh = document.createElement('img');
                    hinhAnh.src = anh;
                    hinhAnh.alt = activeGalleryRoomName + ' - thumbnail ' + (index + 1);

                    nutAnh.appendChild(hinhAnh);
                    nutAnh.addEventListener('click', () => {
                        activeGalleryIndex = index;
                        capNhatModalAnhPhong();
                    });

                    roomGalleryThumbs.appendChild(nutAnh);
                });
            }
        }

        function moModalAnhPhong(roomId, imageIndex) {
            const roomCard = roomCards.find((item) => String(item.dataset.roomId) === String(roomId));

            if (!roomCard) {
                return;
            }

            const danhSachAnh = layDanhSachAnhPhong(roomCard);

            if (!danhSachAnh.length) {
                return;
            }

            activeGalleryImages = danhSachAnh;
            activeGalleryIndex = Math.max(0, Math.min(Number(imageIndex) || 0, danhSachAnh.length - 1));
            activeGalleryRoomName = roomCard.dataset.roomName || 'Ảnh phòng';

            capNhatModalAnhPhong();
            roomGalleryModal?.show();
        }

        roomButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const roomId = button.dataset.roomId;
                setSelectedRoom(roomId);
                document.getElementById('booking-form')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        galleryTriggers.forEach((button) => {
            button.addEventListener('click', () => {
                moModalAnhPhong(button.dataset.roomId, button.dataset.imageIndex);
            });
        });

        roomGalleryPrev?.addEventListener('click', () => {
            if (activeGalleryImages.length <= 1) {
                return;
            }

            activeGalleryIndex = (activeGalleryIndex - 1 + activeGalleryImages.length) % activeGalleryImages.length;
            capNhatModalAnhPhong();
        });

        roomGalleryNext?.addEventListener('click', () => {
            if (activeGalleryImages.length <= 1) {
                return;
            }

            activeGalleryIndex = (activeGalleryIndex + 1) % activeGalleryImages.length;
            capNhatModalAnhPhong();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && (document.body.classList.contains('search-panel-open') || document.body.classList.contains('auth-panel-open'))) {
                closeFloatingPanels();
                return;
            }

            if (!roomGalleryModalElement?.classList.contains('show') || activeGalleryImages.length <= 1) {
                return;
            }

            if (event.key === 'ArrowLeft') {
                roomGalleryPrev?.click();
            }

            if (event.key === 'ArrowRight') {
                roomGalleryNext?.click();
            }
        });

        if (roomInput && roomInput.value) {
            setSelectedRoom(roomInput.value);
        }

        if (initialAuthPanel) {
            setAuthPanelOpen(initialAuthPanel);
        }
    </script>
</body>
</html>
