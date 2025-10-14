<?php
session_start();

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="index.css?v=1.2">
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <a href="index.php">Moje Webová Stránka</a>
    </div>
    <ul class="navbar-menu">
        <li><a href="index.php">Home</a></li>

        <?php if(isset($_SESSION['login'])): ?>
            <li>
                <?php echo htmlspecialchars($_SESSION['name'] . " " . $_SESSION['surname']); ?>
            </li>
            <li>
                <form action="views/logout.php" method="post">
                    <button type="submit">Odhlásit se</button>
                </form>
            </li>
        <?php else: ?>
            <li>
                <form action="views/login.php" method="get">
                    <button type="submit">Přihlásit se</button>
                </form>
            </li>
            <li>
                <form action="views/register.php" method="get">
                    <button type="submit">Registrovat se</button>
                </form>
            </li>
        <?php endif; ?>

    </ul>
</nav>


<main class="container">
    <h1 class="conference-title">Vědecká konference počítačové grafiky a vizualizace</h1>

    <p>
        Vědecká konference počítačové grafiky a vizualizace je odborné setkání zaměřené na aktuální výzkum, inovace
        a aplikace v oblasti počítačové grafiky, vizualizačních technik a interaktivních systémů. Cílem konference je
        propojit akademickou sféru, výzkumné instituce a profesionální vývojáře a poskytnout platformu pro sdílení
        nejnovějších poznatků, technologických pokroků a zkušeností v oblasti vizualizace dat, počítačové grafiky
        a souvisejících výpočetních metod.
    </p>

    <h2>Oblast zájmu konference</h2>

    <p>
        Konference pokrývá širokou škálu témat, která se vztahují k počítačové grafice, vizualizaci a interaktivním
        technologiím. Mezi hlavní oblasti patří:
    </p>

    <h3>Počítačová grafika a renderování</h3>
    <p>
        Počítačová grafika představuje základní pilíř vizualizačních technik. Účastníci se zaměřují na nové metody
        generování realistických obrazů, včetně fyzikálně založeného renderování, ray tracingu a hybridních technik.
        Výzkum se rovněž soustředí na optimalizaci výkonu, efektivní využití výpočetních zdrojů a algoritmy pro
        real-time grafiku, která je klíčová pro interaktivní aplikace a herní průmysl.
    </p>

    <h3>Vizualizace dat a informací</h3>
    <p>
        Vizualizace dat je nezbytná pro interpretaci velkých a složitých datových souborů. Konference zahrnuje
        prezentace o technikách vizualizace strukturovaných a nestrukturovaných dat, interaktivních vizualizačních
        rozhraních a nových metodách vizualizace ve 2D a 3D. Obzvláště důležitá je vizualizace ve vědeckém
        výzkumu, medicíně, inženýrství a geografických informačních systémech.
    </p>

    <h3>Interaktivní technologie a HCI</h3>
    <p>
        Konference se zaměřuje i na interakci mezi uživatelem a vizualizačním prostředím. Diskutují se nové metody
        ovládání a interakce, včetně gest, dotykových rozhraní, virtuální a rozšířené reality. Cílem je zlepšit
        uživatelský zážitek a efektivitu práce s vizualizačními nástroji.
    </p>

    <h3>Technologické pokroky a inovace</h3>
    <p>
        Konference prezentuje nejnovější technologické pokroky v oblasti počítačové grafiky a vizualizace, jako jsou
        GPU akcelerované renderování, paralelní výpočty, algoritmy strojového učení pro vizualizaci dat, a nové
        standardy pro 3D modelování a animaci. Diskutuje se rovněž integrace umělé inteligence do vizualizačních
        aplikací a automatizace procesů při analýze velkých dat.
    </p>

    <h3>Publikum a účastníci</h3>
    <p>
        Konference je určena pro akademiky, výzkumné pracovníky, studenty vysokých škol a profesionály z oblasti
        počítačové grafiky, vizualizace a souvisejících oborů. Účastníci mají možnost prezentovat své studie,
        diskutovat o výsledcích výzkumu a navazovat odborné kontakty.
    </p>

    <h3>Cíle konference</h3>
    <ul>
        <li>Podpora interdisciplinární spolupráce mezi akademickými institucemi a průmyslem.</li>
        <li>Prezentace nejnovějších vědeckých poznatků a technologických inovací.</li>
        <li>Propojení výzkumníků a profesionálů v oblasti vizualizace a počítačové grafiky.</li>
        <li>Diskuse o budoucích trendech a výzvách v oblasti vizualizačních technik a interaktivních systémů.</li>
    </ul>

    <p>
        Celkově konference představuje jedinečnou platformu pro sdílení znalostí, výměnu zkušeností a budování
        odborných sítí. Účastníci získají nejen přehled o aktuálních trendech a metodách, ale také inspiraci pro
        vlastní výzkum a aplikace v oblasti počítačové grafiky a vizualizace.
    </p>

</main>


</body>
</html>