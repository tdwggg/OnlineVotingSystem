<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Browse Candidates - iVotePH</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        :root {
            --userBlue: #0646a8;
            --userBlueDark: #0b3f91;
            --userBlueSoft: #eaf2ff;
            --userInk: #172033;
            --userMuted: #667085;
            --userLine: #dce5f2;
            --userShadow: 0 14px 34px rgba(11, 36, 71, 0.10);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            width: 100%;
            min-height: 100%;
            overflow-x: hidden;
        }

        body.userPage {
            background:
                linear-gradient(180deg, rgba(244, 248, 255, 0.94), rgba(247, 249, 252, 0.98)),
                url("flag-bg.png") center top / cover fixed no-repeat;
            color: var(--userInk);
            font-family: Inter, "Segoe UI", Arial, sans-serif;
        }

        .videoBg {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.10;
            z-index: -2;
            pointer-events: none;
        }

        .appBackdrop {
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(216, 32, 42, 0.08), transparent 30%),
                radial-gradient(circle at top left, rgba(6, 70, 168, 0.14), transparent 32%);
            z-index: -1;
            pointer-events: none;
        }

        .userTopbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 12px 18px;
            background: rgba(255, 255, 255, 0.94);
            border-bottom: 1px solid rgba(210, 219, 235, 0.95);
            box-shadow: 0 10px 28px rgba(16, 24, 40, 0.08);
            backdrop-filter: blur(18px);
        }

        .userTopbarInner {
            width: 100%;
            max-width: 1480px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: auto minmax(170px, 280px) minmax(500px, 1fr) auto;
            align-items: center;
            gap: 7px;
        }

        .brandLink {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 5px 8px;
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid var(--userLine);
            text-decoration: none;
        }

        .brandLogo {
            display: block;
            width: 62px !important;
            max-width: 62px !important;
            height: auto !important;
            max-height: 32px !important;
            object-fit: contain !important;
        }

        .topbarSearch {
            width: 100%;
            min-width: 0;
        }

        .topbarSearch .input-group {
            height: 42px;
            border-radius: 999px;
            overflow: hidden;
            background: #f3f4fb;
        }

        .topbarSearch .input-group-text {
            border: none;
            background: #f3f4fb;
            padding-left: 15px;
            padding-right: 7px;
            color: var(--userMuted);
        }

        .searchInput {
            height: 42px !important;
            border: none !important;
            background: #f3f4fb !important;
            box-shadow: none !important;
            font-size: 12px;
            padding-left: 4px;
        }

        .userNavBar {
            background: transparent !important;
            padding: 0 !important;
            overflow: hidden !important;
        }

        .userNavInner {
            width: 100%;
            overflow: hidden;
        }

        .userNavList {
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center;
            justify-content: flex-start;
            gap: 4px;
            overflow: hidden;
            min-width: 0;
        }

        .userNavList li {
            list-style: none !important;
            min-width: 0;
        }

        .userNavList a {
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 8px 7px;
            border-radius: 999px;
            background: #f4f6fb;
            color: #3f4350;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap;
            text-decoration: none;
            box-shadow: 0 8px 18px rgba(16, 24, 40, 0.04);
            transition: 0.2s ease;
        }

        .userNavList a:hover {
            background: #e9f1ff;
            color: var(--userBlue);
            transform: translateY(-1px);
        }

        .userNavList a.active {
            background: #0b5ed7;
            color: #ffffff;
            box-shadow: 0 10px 22px rgba(6, 70, 168, 0.22);
        }

        .userNavList a i {
            font-size: 10px;
            color: inherit;
        }

        .userChip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 44px;
            padding: 6px 8px 6px 6px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid var(--userLine);
            color: var(--userInk);
            text-decoration: none;
            box-shadow: 0 10px 22px rgba(16, 24, 40, 0.08);
            cursor: pointer;
            white-space: nowrap;
        }

        .userAvatarCircle {
            width: 34px;
            height: 34px;
            min-width: 34px;
            border-radius: 50%;
            background: #0b5ed7;
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 900;
        }

        .userName {
            font-size: 10.5px;
            font-weight: 900;
            color: var(--userInk);
            line-height: 1.1;
        }

        .verifiedBadge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #0b5ed7;
            font-size: 8.5px;
            font-weight: 800;
            line-height: 1.1;
        }

        .userMain {
            width: 100%;
            max-width: 1480px;
            margin: 0 auto;
            padding: 24px 22px 40px;
        }

        .userCard {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid var(--userLine);
            border-radius: 24px;
            box-shadow: var(--userShadow);
        }

        .candidateHero {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 18px;
            align-items: center;
            padding: 28px;
            margin-bottom: 18px;
            background:
                radial-gradient(circle at top right, rgba(247, 201, 72, 0.22), transparent 32%),
                linear-gradient(135deg, #0646a8 0%, #0b3f91 100%);
            color: #ffffff;
            overflow: hidden;
        }

        .candidateHeroEyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 13px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.16);
            font-size: 12px;
            font-weight: 900;
            margin-bottom: 14px;
        }

        .candidateHero h1 {
            margin: 0;
            font-size: clamp(2rem, 4vw, 3.3rem);
            line-height: 1;
            font-weight: 950;
            letter-spacing: -0.05em;
        }

        .candidateHero p {
            margin: 12px 0 0;
            max-width: 760px;
            color: rgba(255, 255, 255, 0.88);
            font-size: 15px;
            line-height: 1.6;
        }

        .candidateHeroPill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 10px 16px;
            border-radius: 999px;
            background: #ffffff;
            color: var(--userBlue);
            font-size: 13px;
            font-weight: 900;
            white-space: nowrap;
        }

        .candidateControls {
            display: grid;
            grid-template-columns: minmax(280px, 1fr) 220px 160px;
            gap: 12px;
            align-items: end;
            padding: 18px;
            margin-bottom: 18px;
        }

        .candidateControls .form-label {
            font-size: 12px;
            font-weight: 900;
            color: var(--userInk);
            margin-bottom: 7px;
        }

        .candidateControls .form-control,
        .candidateControls .form-select,
        .candidateControls .btn {
            height: 44px;
            border-radius: 14px;
            font-size: 13px;
        }

        .candidateGrid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .candidateCard {
            border: 1px solid var(--userLine);
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 12px 28px rgba(11, 36, 71, 0.08);
            overflow: hidden;
            transition: 0.22s ease;
        }

        .candidateCard:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 34px rgba(6, 70, 168, 0.14);
            border-color: rgba(6, 70, 168, 0.35);
        }

        .candidatePhoto {
            height: 150px;
            background:
                radial-gradient(circle at top right, rgba(247, 201, 72, 0.24), transparent 34%),
                linear-gradient(135deg, #eaf2ff, #ffffff);
            color: var(--userBlue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 46px;
        }

        .candidateCard .card-body {
            padding: 18px;
        }

        .candidateParty {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: 6px 10px;
            border-radius: 999px;
            background: var(--userBlueSoft);
            color: var(--userBlue);
            font-size: 11px;
            font-weight: 900;
            margin-bottom: 12px;
        }

        .candidateName {
            margin: 0 0 6px;
            color: var(--userInk);
            font-size: 20px;
            line-height: 1.15;
            font-weight: 950;
            letter-spacing: -0.03em;
        }

        .candidatePosition {
            color: var(--userMuted);
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 10px;
        }

        .candidateCard p {
            min-height: 60px;
            color: var(--userMuted);
            font-size: 13px;
            line-height: 1.45;
            margin-bottom: 16px;
        }

        .profileLink {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--userBlue);
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
        }

        .profileLink:hover {
            color: var(--userBlueDark);
        }

        .footer {
            padding: 18px 22px;
            text-align: center;
            color: var(--userMuted);
            font-size: 13px;
        }

        /* PROFILE MODAL - FIXED */
        #profileModal .modal-dialog {
            max-width: min(1180px, calc(100vw - 24px));
            margin: 12px auto;
        }

        #profileModal .profileModalContent {
            max-height: calc(100vh - 24px);
            display: flex;
            flex-direction: column;
        }

        .profileModalContent,
        .candidateModalContent {
            border: none;
            border-radius: 26px;
            overflow: hidden;
            box-shadow: 0 24px 70px rgba(16, 24, 40, 0.22);
        }

        .profileModalHeader,
        .candidateModalHeader {
            flex: 0 0 auto;
            background:
                radial-gradient(circle at top right, rgba(247, 201, 72, 0.28), transparent 34%),
                linear-gradient(135deg, #0646a8 0%, #0b3f91 100%);
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }

        .profileModalAvatar,
        .candidateModalAvatar {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            background: #ffffff;
            color: var(--userBlue);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 950;
            box-shadow: 0 12px 24px rgba(16, 24, 40, 0.16);
            margin-bottom: 10px;
        }

        .profileModalHeader h5,
        .candidateModalHeader h5 {
            margin: 0;
            font-size: 22px;
            font-weight: 950;
        }

        .profileModalHeader p,
        .candidateModalHeader p {
            margin: 6px 0 0;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.86);
        }

        .profileModalBody {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            padding: 22px;
            background: #ffffff;
        }

        .profileReadOnlyNote,
        .requestNotice {
            margin-bottom: 18px;
            padding: 14px;
            border-radius: 16px;
            background: #eaf2ff;
            border: 1px solid #cfe0ff;
            color: var(--userBlue);
            font-size: 13px;
            font-weight: 800;
            line-height: 1.5;
        }

        .profileSectionTitle {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 18px 0 12px;
            color: var(--userInk);
            font-size: 14px;
            font-weight: 950;
        }

        .profileSectionTitle:first-of-type {
            margin-top: 0;
        }

        .profileSectionTitle i {
            color: var(--userBlue);
        }

        .profileFullGrid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .profileFullGrid.threeCols {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .profileFullItem {
            min-width: 0;
            background: #f7f9fd;
            border: 1px solid #e1e8f3;
            border-radius: 16px;
            padding: 13px;
        }

        .profileFullItem.profileFullWide {
            grid-column: 1 / -1;
        }

        .profileFullItem span {
            display: block;
            font-size: 10.5px;
            font-weight: 900;
            color: var(--userMuted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 5px;
        }

        .profileFullItem strong {
            display: block;
            font-size: 14px;
            font-weight: 900;
            color: var(--userInk);
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .profileModalActions {
            flex: 0 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            padding: 14px 22px;
            background: #ffffff;
            border-top: 1px solid #e1e8f3;
            box-shadow: 0 -10px 24px rgba(16, 24, 40, 0.06);
        }

        .profileModalActions .btn {
            min-height: 46px;
            border-radius: 14px;
            font-weight: 900;
            font-size: 13px;
        }

        .requestModalBody,
        .candidateModalBody {
            padding: 22px;
        }

        .requestModalBody .form-label {
            font-size: 12px;
            font-weight: 900;
            color: var(--userInk);
            margin-bottom: 7px;
        }

        .requestModalBody .form-select,
        .requestModalBody .form-control {
            border-radius: 14px;
            border: 1px solid var(--userLine);
            font-size: 13px;
            box-shadow: none;
        }

        .requestModalBody .form-control:focus,
        .requestModalBody .form-select:focus {
            border-color: #0b5ed7;
            box-shadow: 0 0 0 4px rgba(11, 94, 215, 0.12);
        }

        .candidateModalBody p {
            color: var(--userMuted);
            line-height: 1.65;
            margin-bottom: 0;
        }

        .candidateModalMeta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .candidateModalMetaItem {
            background: #f7f9fd;
            border: 1px solid #e1e8f3;
            border-radius: 16px;
            padding: 13px;
        }

        .candidateModalMetaItem span {
            display: block;
            font-size: 11px;
            font-weight: 900;
            color: var(--userMuted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 5px;
        }

        .candidateModalMetaItem strong {
            display: block;
            font-size: 14px;
            font-weight: 900;
            color: var(--userInk);
        }

        .userPageMotion,
        .userCard,
        .candidateCard {
            animation: userFadeUp 0.35s ease both;
        }

        @keyframes userFadeUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
                scroll-behavior: auto !important;
            }
        }

        @media (max-width: 1180px) {
            .userTopbarInner {
                grid-template-columns: auto 1fr auto;
                grid-template-rows: auto auto auto;
            }

            .brandLink {
                grid-column: 1;
                grid-row: 1;
            }

            .userChip {
                grid-column: 3;
                grid-row: 1;
            }

            .topbarSearch {
                grid-column: 1 / -1;
                grid-row: 2;
            }

            .userNavBar {
                grid-column: 1 / -1;
                grid-row: 3;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            .userNavInner {
                overflow-x: auto;
                scrollbar-width: none;
            }

            .userNavInner::-webkit-scrollbar {
                display: none;
            }

            .userNavList {
                min-width: max-content;
                width: max-content;
            }

            .candidateHero {
                grid-template-columns: 1fr;
            }

            .candidateGrid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .candidateControls {
                grid-template-columns: 1fr 200px 150px;
            }

            .profileFullGrid.threeCols {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .userTopbar {
                padding: 10px 12px;
            }

            .brandLogo {
                width: 60px !important;
                max-width: 60px !important;
            }

            .userChip {
                padding: 6px;
            }

            .userMeta,
            .userChip .fa-chevron-down {
                display: none !important;
            }

            .userMain {
                padding: 14px 12px 30px;
            }

            .candidateHero {
                padding: 24px 20px;
            }

            .candidateControls {
                grid-template-columns: 1fr;
            }

            .candidateGrid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            #profileModal .modal-dialog {
                max-width: calc(100vw - 16px);
                margin: 8px auto;
            }

            #profileModal .profileModalContent {
                max-height: calc(100vh - 16px);
                border-radius: 20px;
            }

            .profileModalHeader {
                padding: 20px 16px;
            }

            .profileModalAvatar {
                width: 62px;
                height: 62px;
                font-size: 20px;
            }

            .profileModalBody,
            .requestModalBody,
            .candidateModalBody {
                padding: 16px;
            }

            .profileFullGrid,
            .profileFullGrid.threeCols,
            .profileModalActions,
            .candidateModalMeta {
                grid-template-columns: 1fr;
            }

            .profileModalActions {
                padding: 12px 16px;
            }
        }

        @media (max-width: 480px) {
            .candidateGrid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body class="userPage">
    <video class="videoBg" autoplay muted loop playsinline>
        <source src="flag.mp4" type="video/mp4">
    </video>

    <div class="appBackdrop"></div>

    <header class="userTopbar">
        <div class="userTopbarInner">
            <a href="index.html" class="brandLink" aria-label="iVotePH Home">
                <img src="FINALS 2.png" class="brandLogo" alt="iVotePH">
            </a>

            <div class="topbarSearch">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="search" class="form-control searchInput" id="candidateSearch" placeholder="Search candidates, party, or position">
                </div>
            </div>

            <nav class="userNavBar" aria-label="User navigation">
                <div class="userNavInner">
                    <ul class="userNavList">
                        <li>
                            <a href="index.html">
                                <i class="fa-solid fa-landmark"></i>
                                Home
                            </a>
                        </li>

                        <li>
                            <a href="about.html">
                                <i class="fa-solid fa-circle-info"></i>
                                About
                            </a>
                        </li>

                        <li>
                            <a href="browsecandi.html" class="active">
                                <i class="fa-solid fa-users"></i>
                                Candidates
                            </a>
                        </li>

                        <li>
                            <a href="startvoting.html">
                                <i class="fa-solid fa-check-to-slot"></i>
                                Voting
                            </a>
                        </li>

                        <li>
                            <a href="myballot.html">
                                <i class="fa-solid fa-file-signature"></i>
                                My Ballot
                            </a>
                        </li>

                        <li>
                            <a href="results.html">
                                <i class="fa-solid fa-chart-simple"></i>
                                Results
                            </a>
                        </li>

                        <li>
                            <a href="help.html">
                                <i class="fa-solid fa-circle-question"></i>
                                Help
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <button type="button" class="userChip border-0" data-bs-toggle="modal" data-bs-target="#profileModal">
                <span class="userAvatarCircle">JD</span>
                <span class="userMeta">
                    <span class="userName d-block">Juan Dela Cruz</span>
                    <span class="verifiedBadge">
                        <i class="fa-solid fa-circle-check"></i>
                        Verified Voter
                    </span>
                </span>
                <i class="fa-solid fa-chevron-down text-muted small d-none d-md-inline"></i>
            </button>
        </div>
    </header>

    <main class="userMain userPageMotion">
        <section class="candidateHero userCard">
            <div>
                <div class="candidateHeroEyebrow">
                    <i class="fa-solid fa-users"></i>
                    Candidate Directory
                </div>

                <h1>Browse Candidates</h1>

                <p>
                    Review candidate profiles, parties, positions, and platforms before casting your official ballot.
                </p>
            </div>

            <span class="candidateHeroPill">
                <i class="fa-solid fa-circle-info"></i>
                Sample Frontend Data
            </span>
        </section>

        <section class="candidateControls userCard">
            <div>
                <label class="form-label" for="candidateSearchPanel">Search</label>
                <input type="search" class="form-control" id="candidateSearchPanel" placeholder="Search candidate name, party, or platform">
            </div>

            <div>
                <label class="form-label" for="positionFilter">Position</label>
                <select class="form-select" id="positionFilter">
                    <option value="">All positions</option>
                    <option value="President">President</option>
                    <option value="Vice President">Vice President</option>
                    <option value="Senator">Senator</option>
                    <option value="Party List">Party List</option>
                </select>
            </div>

            <div>
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-primary w-100" onclick="resetCandidateFilters()">
                    Reset
                </button>
            </div>
        </section>

        <section class="candidateGrid" id="candidateGrid">
            <article class="candidateCard" data-name="Maria Clara Santos" data-party="National Progress Party" data-position="President" data-platform="Healthcare access, education support, and clean energy investment.">
                <div class="candidatePhoto">
                    <i class="fa-solid fa-user-tie"></i>
                </div>

                <div class="card-body">
                    <div class="candidateParty">National Progress Party</div>
                    <h3 class="candidateName">Maria Clara Santos</h3>
                    <div class="candidatePosition">President</div>
                    <p>Healthcare access, education support, and clean energy investment.</p>
                    <button type="button" class="profileLink border-0 bg-transparent p-0" onclick="showCandidateDetails('Maria Clara Santos', 'President', 'National Progress Party', 'Healthcare access, education support, and clean energy investment.')">
                        View details
                        <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </article>

            <article class="candidateCard" data-name="Arthur Pendelton" data-party="Federal Alliance" data-position="President" data-platform="Local economic growth, infrastructure, and tax reform.">
                <div class="candidatePhoto">
                    <i class="fa-solid fa-user-tie"></i>
                </div>

                <div class="card-body">
                    <div class="candidateParty">Federal Alliance</div>
                    <h3 class="candidateName">Arthur Pendelton</h3>
                    <div class="candidatePosition">President</div>
                    <p>Local economic growth, infrastructure, and tax reform.</p>
                    <button type="button" class="profileLink border-0 bg-transparent p-0" onclick="showCandidateDetails('Arthur Pendelton', 'President', 'Federal Alliance', 'Local economic growth, infrastructure, and tax reform.')">
                        View details
                        <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </article>

            <article class="candidateCard" data-name="Clarissa Ong-Ramos" data-party="National Progress Party" data-position="Vice President" data-platform="Community clinics, renewable energy, and social services.">
                <div class="candidatePhoto">
                    <i class="fa-solid fa-user-tie"></i>
                </div>

                <div class="card-body">
                    <div class="candidateParty">National Progress Party</div>
                    <h3 class="candidateName">Clarissa Ong-Ramos</h3>
                    <div class="candidatePosition">Vice President</div>
                    <p>Community clinics, renewable energy, and social services.</p>
                    <button type="button" class="profileLink border-0 bg-transparent p-0" onclick="showCandidateDetails('Clarissa Ong-Ramos', 'Vice President', 'National Progress Party', 'Community clinics, renewable energy, and social services.')">
                        View details
                        <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </article>

            <article class="candidateCard" data-name="Benjamin G. Go" data-party="Federal Alliance" data-position="Vice President" data-platform="Transport corridors and provincial infrastructure development.">
                <div class="candidatePhoto">
                    <i class="fa-solid fa-user-tie"></i>
                </div>

                <div class="card-body">
                    <div class="candidateParty">Federal Alliance</div>
                    <h3 class="candidateName">Benjamin G. Go</h3>
                    <div class="candidatePosition">Vice President</div>
                    <p>Transport corridors and provincial infrastructure development.</p>
                    <button type="button" class="profileLink border-0 bg-transparent p-0" onclick="showCandidateDetails('Benjamin G. Go', 'Vice President', 'Federal Alliance', 'Transport corridors and provincial infrastructure development.')">
                        View details
                        <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </article>

            <article class="candidateCard" data-name="Elena Torralba" data-party="National Progress Party" data-position="Senator" data-platform="Scholarships and public school modernization.">
                <div class="candidatePhoto">
                    <i class="fa-solid fa-user-tie"></i>
                </div>

                <div class="card-body">
                    <div class="candidateParty">National Progress Party</div>
                    <h3 class="candidateName">Elena Torralba</h3>
                    <div class="candidatePosition">Senator</div>
                    <p>Scholarships and public school modernization.</p>
                    <button type="button" class="profileLink border-0 bg-transparent p-0" onclick="showCandidateDetails('Elena Torralba', 'Senator', 'National Progress Party', 'Scholarships and public school modernization.')">
                        View details
                        <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </article>

            <article class="candidateCard" data-name="Rico Sison" data-party="Federal Alliance" data-position="Senator" data-platform="Cybersecurity and startup development.">
                <div class="candidatePhoto">
                    <i class="fa-solid fa-user-tie"></i>
                </div>

                <div class="card-body">
                    <div class="candidateParty">Federal Alliance</div>
                    <h3 class="candidateName">Rico Sison</h3>
                    <div class="candidatePosition">Senator</div>
                    <p>Cybersecurity and startup development.</p>
                    <button type="button" class="profileLink border-0 bg-transparent p-0" onclick="showCandidateDetails('Rico Sison', 'Senator', 'Federal Alliance', 'Cybersecurity and startup development.')">
                        View details
                        <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </article>

            <article class="candidateCard" data-name="BAYANIHAN" data-party="Community Service" data-position="Party List" data-platform="Medical kits and community micro-financing.">
                <div class="candidatePhoto">
                    <i class="fa-solid fa-people-group"></i>
                </div>

                <div class="card-body">
                    <div class="candidateParty">Community Service</div>
                    <h3 class="candidateName">BAYANIHAN</h3>
                    <div class="candidatePosition">Party List</div>
                    <p>Medical kits and community micro-financing.</p>
                    <button type="button" class="profileLink border-0 bg-transparent p-0" onclick="showCandidateDetails('BAYANIHAN', 'Party List', 'Community Service', 'Medical kits and community micro-financing.')">
                        View details
                        <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </article>

            <article class="candidateCard" data-name="KALIKASAN" data-party="Environmental Protection" data-position="Party List" data-platform="Coastal protection and local sustainability projects.">
                <div class="candidatePhoto">
                    <i class="fa-solid fa-seedling"></i>
                </div>

                <div class="card-body">
                    <div class="candidateParty">Environmental Protection</div>
                    <h3 class="candidateName">KALIKASAN</h3>
                    <div class="candidatePosition">Party List</div>
                    <p>Coastal protection and local sustainability projects.</p>
                    <button type="button" class="profileLink border-0 bg-transparent p-0" onclick="showCandidateDetails('KALIKASAN', 'Party List', 'Environmental Protection', 'Coastal protection and local sustainability projects.')">
                        View details
                        <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </article>
        </section>
    </main>

    <!-- FULL READ-ONLY PROFILE MODAL -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content profileModalContent">
                <div class="profileModalHeader">
                    <div class="profileModalAvatar">JD</div>
                    <h5 id="profileModalLabel">Juan Dela Cruz</h5>
                    <p>
                        <i class="fa-solid fa-circle-check me-1"></i>
                        Verified Registered Voter
                    </p>
                </div>

                <div class="profileModalBody">
                    <div class="profileReadOnlyNote">
                        <i class="fa-solid fa-circle-info me-2"></i>
                        This profile is read-only. Registered voters can review their submitted information anytime,
                        but corrections must be requested through the admin.
                    </div>

                    <div class="profileSectionTitle">
                        <i class="fa-solid fa-id-card"></i>
                        Account Information
                    </div>

                    <div class="profileFullGrid threeCols">
                        <div class="profileFullItem">
                            <span>Voter ID</span>
                            <strong>VOTER-001</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>Registration Status</span>
                            <strong>Registered</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>Profile Status</span>
                            <strong>Complete</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>Ballot Status</span>
                            <strong>Not Submitted</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>Account Type</span>
                            <strong>Voter</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>Account Access</span>
                            <strong>Active</strong>
                        </div>
                    </div>

                    <div class="profileSectionTitle">
                        <i class="fa-solid fa-user"></i>
                        Personal Information
                    </div>

                    <div class="profileFullGrid threeCols">
                        <div class="profileFullItem">
                            <span>First Name</span>
                            <strong>Juan</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>Middle Name</span>
                            <strong>Santos</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>Last Name</span>
                            <strong>Dela Cruz</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>Birth Date</span>
                            <strong>January 15, 2001</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>Sex</span>
                            <strong>Male</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>Civil Status</span>
                            <strong>Single</strong>
                        </div>
                    </div>

                    <div class="profileSectionTitle">
                        <i class="fa-solid fa-address-book"></i>
                        Contact Information
                    </div>

                    <div class="profileFullGrid">
                        <div class="profileFullItem">
                            <span>Email Address</span>
                            <strong>juan@example.com</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>Mobile Number</span>
                            <strong>0917 123 4567</strong>
                        </div>
                    </div>

                    <div class="profileSectionTitle">
                        <i class="fa-solid fa-location-dot"></i>
                        Registered Address
                    </div>

                    <div class="profileFullGrid threeCols">
                        <div class="profileFullItem">
                            <span>Region</span>
                            <strong>National Capital Region</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>Province</span>
                            <strong>Metro Manila</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>City / Municipality</span>
                            <strong>Quezon City</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>Barangay</span>
                            <strong>Commonwealth</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>ZIP Code</span>
                            <strong>1121</strong>
                        </div>

                        <div class="profileFullItem">
                            <span>Country</span>
                            <strong>Philippines</strong>
                        </div>

                        <div class="profileFullItem profileFullWide">
                            <span>Complete Address</span>
                            <strong>123 Sample Street, Barangay Commonwealth, Quezon City, Metro Manila</strong>
                        </div>
                    </div>
                </div>

                <div class="profileModalActions">
                    <button type="button" class="btn btn-primary" onclick="openProfileRequestModal()">
                        <i class="fa-solid fa-pen-to-square me-1"></i>
                        Request Change
                    </button>

                    <button type="button" class="btn btn-danger" onclick="logoutUser()">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ADMIN REQUEST MODAL -->
    <div class="modal fade" id="profileRequestModal" tabindex="-1" aria-labelledby="profileRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content profileModalContent">
                <div class="profileModalHeader">
                    <div class="profileModalAvatar">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <h5 id="profileRequestModalLabel">Request Profile Change</h5>
                    <p>
                        Registered voter information cannot be edited directly.
                    </p>
                </div>

                <div class="requestModalBody">
                    <div class="requestNotice">
                        <i class="fa-solid fa-circle-info me-2"></i>
                        Submit a request to the admin if your registered name or personal details need correction.
                    </div>

                    <form id="profileChangeRequestForm" onsubmit="submitProfileChangeRequest(event)">
                        <div class="mb-3">
                            <label for="requestField" class="form-label">Information to change</label>
                            <select class="form-select" id="requestField" required>
                                <option value="">Select information</option>
                                <option value="Full Name">Full Name</option>
                                <option value="Email Address">Email Address</option>
                                <option value="Mobile Number">Mobile Number</option>
                                <option value="Birth Date">Birth Date</option>
                                <option value="Sex">Sex</option>
                                <option value="Registered Address">Registered Address</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="requestMessage" class="form-label">Reason / Correct Information</label>
                            <textarea class="form-control" id="requestMessage" rows="4" required placeholder="Example: My registered last name is misspelled. It should be Dela Cruz."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3 rounded-4 fw-bold">
                                <i class="fa-solid fa-paper-plane me-2"></i>
                                Submit Request
                            </button>

                            <button type="button" class="btn btn-light py-3 rounded-4 fw-bold" data-bs-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="candidateModal" tabindex="-1" aria-labelledby="candidateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content candidateModalContent">
                <div class="candidateModalHeader">
                    <div class="candidateModalAvatar">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <h5 id="candidateModalLabel">Candidate Name</h5>
                    <p id="candidateModalSub">Position</p>
                </div>

                <div class="candidateModalBody">
                    <div class="candidateModalMeta">
                        <div class="candidateModalMetaItem">
                            <span>Position</span>
                            <strong id="candidateModalPosition">-</strong>
                        </div>

                        <div class="candidateModalMetaItem">
                            <span>Party</span>
                            <strong id="candidateModalParty">-</strong>
                        </div>
                    </div>

                    <div class="candidateModalMetaItem">
                        <span>Platform</span>
                        <p id="candidateModalPlatform">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div>© 2026 iVotePH. Secure. Accessible. Transparent.</div>
    </footer>

    <script>
        function normalizeText(value) {
            return String(value || '').toLowerCase().trim();
        }

        function filterCandidates() {
            var searchInput = document.getElementById('candidateSearch');
            var searchPanel = document.getElementById('candidateSearchPanel');
            var positionFilter = document.getElementById('positionFilter');
            var query = normalizeText(searchPanel.value || searchInput.value);
            var position = positionFilter.value;
            var cards = document.querySelectorAll('.candidateCard');

            for (var i = 0; i < cards.length; i++) {
                var card = cards[i];
                var combined = normalizeText(
                    card.getAttribute('data-name') + ' ' +
                    card.getAttribute('data-party') + ' ' +
                    card.getAttribute('data-position') + ' ' +
                    card.getAttribute('data-platform')
                );

                var matchesSearch = query === '' || combined.indexOf(query) !== -1;
                var matchesPosition = position === '' || card.getAttribute('data-position') === position;

                card.style.display = matchesSearch && matchesPosition ? '' : 'none';
            }
        }

        function syncSearch(sourceId) {
            var searchInput = document.getElementById('candidateSearch');
            var searchPanel = document.getElementById('candidateSearchPanel');

            if (sourceId === 'top') {
                searchPanel.value = searchInput.value;
            } else {
                searchInput.value = searchPanel.value;
            }

            filterCandidates();
        }

        function resetCandidateFilters() {
            document.getElementById('candidateSearch').value = '';
            document.getElementById('candidateSearchPanel').value = '';
            document.getElementById('positionFilter').value = '';
            filterCandidates();
        }

        function showCandidateDetails(name, position, party, platform) {
            document.getElementById('candidateModalLabel').textContent = name;
            document.getElementById('candidateModalSub').textContent = position + ' • ' + party;
            document.getElementById('candidateModalPosition').textContent = position;
            document.getElementById('candidateModalParty').textContent = party;
            document.getElementById('candidateModalPlatform').textContent = platform;

            var modal = new bootstrap.Modal(document.getElementById('candidateModal'));
            modal.show();
        }

        function openProfileRequestModal() {
            var profileModalElement = document.getElementById('profileModal');
            var requestModalElement = document.getElementById('profileRequestModal');

            var profileModal = bootstrap.Modal.getInstance(profileModalElement);
            var requestModal = new bootstrap.Modal(requestModalElement);

            if (profileModal) {
                profileModal.hide();
            }

            setTimeout(function () {
                requestModal.show();
            }, 250);
        }

        function submitProfileChangeRequest(event) {
            event.preventDefault();

            var requestField = document.getElementById('requestField').value;
            var requestMessage = document.getElementById('requestMessage').value;

            if (!requestField || !requestMessage.trim()) {
                alert('Please complete the profile change request form.');
                return;
            }

            alert('Your profile change request has been prepared. Later, this will be sent to the admin side once connected to the database.');

            document.getElementById('profileChangeRequestForm').reset();

            var requestModalElement = document.getElementById('profileRequestModal');
            var requestModal = bootstrap.Modal.getInstance(requestModalElement);

            if (requestModal) {
                requestModal.hide();
            }
        }

        function logoutUser() {
            sessionStorage.removeItem('isLoggedIn');
            sessionStorage.clear();
            window.location.href = 'login.html';
        }

        document.getElementById('candidateSearch').addEventListener('input', function () {
            syncSearch('top');
        });

        document.getElementById('candidateSearchPanel').addEventListener('input', function () {
            syncSearch('panel');
        });

        document.getElementById('positionFilter').addEventListener('change', filterCandidates);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>