<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        /* Styling khusus untuk interaksi SVG */
        polygon {
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        text {
            user-select: none;
        }
    </style>
</head>

<body class="min-h-screen p-4 md:p-8 text-gray-800">

    <div class="max-w-6xl mx-auto">
        <header class="mb-8 text-center md:text-left">
        </header>

        <div class="flex flex-col lg:flex-row gap-6">

            <!-- Area Kiri: Diagram Odontogram -->
            <div class="w-full lg:w-2/3 bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex justify-between items-center border-b pb-4 mb-4">
                    <h2 class="text-xl font-semibold text-gray-700">Diagram Gigi</h2>
                    <div id="status-message"
                        class="text-sm px-3 py-1 bg-blue-50 text-blue-700 rounded-full font-medium opacity-0 transition-opacity duration-300">
                        Siap
                    </div>
                </div>

                <div id="svgselect" class="w-full overflow-x-auto flex justify-center items-center py-4">
                    <!-- viewBox ditambahkan agar SVG responsif menyesuaikan ukuran kontainer -->
                    <svg version="1.1" viewBox="0 0 620 230" class="max-w-full h-auto drop-shadow-md">
                        <g transform="scale(1.5)" id="gmain">
                            <!-- Rahang Atas Kiri (Pasien) -->
                            <g id="P18">
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">18</text>
                            </g>
                            <g id="P17" transform="translate(25,0)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">17</text>
                            </g>
                            <g id="P16" transform="translate(50,0)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">16</text>
                            </g>
                            <g id="P15" transform="translate(75,0)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">15</text>
                            </g>
                            <g id="P14" transform="translate(100,0)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">14</text>
                            </g>
                            <g id="P13" transform="translate(125,0)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">13</text>
                            </g>
                            <g id="P12" transform="translate(150,0)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">12</text>
                            </g>
                            <g id="P11" transform="translate(175,0)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">11</text>
                            </g>

                            <!-- Rahang Atas Kanan (Pasien) -->
                            <g id="P21" transform="translate(210,0)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">21</text>
                            </g>
                            <g id="P22" transform="translate(235,0)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">22</text>
                            </g>
                            <g id="P23" transform="translate(260,0)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">23</text>
                            </g>
                            <g id="P24" transform="translate(285,0)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">24</text>
                            </g>
                            <g id="P25" transform="translate(310,0)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">25</text>
                            </g>
                            <g id="P26" transform="translate(335,0)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">26</text>
                            </g>
                            <g id="P27" transform="translate(360,0)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">27</text>
                            </g>
                            <g id="P28" transform="translate(385,0)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">28</text>
                            </g>

                            <!-- Rahang Atas Kiri (Susu) -->
                            <g id="P55" transform="translate(75,40)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">55</text>
                            </g>
                            <g id="P54" transform="translate(100,40)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">54</text>
                            </g>
                            <g id="P53" transform="translate(125,40)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">53</text>
                            </g>
                            <g id="P52" transform="translate(150,40)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">52</text>
                            </g>
                            <g id="P51" transform="translate(175,40)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">51</text>
                            </g>

                            <!-- Rahang Atas Kanan (Susu) -->
                            <g id="P61" transform="translate(210,40)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">61</text>
                            </g>
                            <g id="P62" transform="translate(235,40)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">62</text>
                            </g>
                            <g id="P63" transform="translate(260,40)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">63</text>
                            </g>
                            <g id="P64" transform="translate(285,40)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">64</text>
                            </g>
                            <g id="P65" transform="translate(310,40)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">65</text>
                            </g>

                            <!-- Rahang Bawah Kiri (Susu) -->
                            <g id="P85" transform="translate(75,80)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">85</text>
                            </g>
                            <g id="P84" transform="translate(100,80)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">84</text>
                            </g>
                            <g id="P83" transform="translate(125,80)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">83</text>
                            </g>
                            <g id="P82" transform="translate(150,80)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">82</text>
                            </g>
                            <g id="P81" transform="translate(175,80)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">81</text>
                            </g>

                            <!-- Rahang Bawah Kanan (Susu) -->
                            <g id="P71" transform="translate(210,80)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">71</text>
                            </g>
                            <g id="P72" transform="translate(235,80)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">72</text>
                            </g>
                            <g id="P73" transform="translate(260,80)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">73</text>
                            </g>
                            <g id="P74" transform="translate(285,80)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">74</text>
                            </g>
                            <g id="P75" transform="translate(310,80)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">75</text>
                            </g>

                            <!-- Rahang Bawah Kiri (Pasien) -->
                            <g id="P48" transform="translate(0,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">48</text>
                            </g>
                            <g id="P47" transform="translate(25,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">47</text>
                            </g>
                            <g id="P46" transform="translate(50,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">46</text>
                            </g>
                            <g id="P45" transform="translate(75,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">45</text>
                            </g>
                            <g id="P44" transform="translate(100,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">44</text>
                            </g>
                            <g id="P43" transform="translate(125,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">43</text>
                            </g>
                            <g id="P42" transform="translate(150,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">42</text>
                            </g>
                            <g id="P41" transform="translate(175,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">41</text>
                            </g>

                            <!-- Rahang Bawah Kanan (Pasien) -->
                            <g id="P31" transform="translate(210,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">31</text>
                            </g>
                            <g id="P32" transform="translate(235,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">32</text>
                            </g>
                            <g id="P33" transform="translate(260,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">33</text>
                            </g>
                            <g id="P34" transform="translate(285,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">34</text>
                            </g>
                            <g id="P35" transform="translate(310,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">35</text>
                            </g>
                            <g id="P36" transform="translate(335,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">36</text>
                            </g>
                            <g id="P37" transform="translate(360,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">37</text>
                            </g>
                            <g id="P38" transform="translate(385,120)">
                                <polygon points="5,5 15,5 15,15 5,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="C"></polygon>
                                <polygon points="0,0 20,0 15,5 5,5" fill="white" stroke="navy" stroke-width="0.5"
                                    id="T"></polygon>
                                <polygon points="5,15 15,15 20,20 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="B"></polygon>
                                <polygon points="15,5 20,0 20,20 15,15" fill="white" stroke="navy" stroke-width="0.5"
                                    id="R"></polygon>
                                <polygon points="0,0 5,5 5,15 0,20" fill="white" stroke="navy" stroke-width="0.5"
                                    id="L"></polygon>
                                <text x="6" y="30" stroke="navy" fill="navy" stroke-width="0.1"
                                    style="font-size: 6pt;font-weight:normal">38</text>
                            </g>
                        </g>
                    </svg>
                </div>
            </div>

            <!-- Area Kanan: Kontrol dan Detail -->
            <div class="w-full lg:w-1/3 flex flex-col gap-6">

                <!-- Info Gigi yang Disorot -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Detail Gigi (Sorotan)</h3>
                    <div class="flex items-center space-x-4 bg-gray-50 p-4 rounded-lg">
                        <div class="flex-1">
                            <span class="block text-sm text-gray-500">Nomor Gigi</span>
                            <span id="piezanumero" class="text-2xl font-bold text-blue-900">--</span>
                        </div>
                        <div class="flex-1">
                            <span class="block text-sm text-gray-500">Permukaan/Sisi</span>
                            <span id="piezacara" class="text-2xl font-bold text-blue-900">-</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">* Keterangan Sisi: T(Atas), B(Bawah), L(Kiri), R(Kanan),
                        C(Tengah)</p>
                </div>

                <!-- Input Perawatan -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 flex-1">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Input Perawatan</h3>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Jenis Tindakan</label>
                        <select id="treatment-select"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border bg-gray-50">
                            <optgroup label="Konservasi / Penambalan">
                                <option selected="selected" value="02.01">Tambalan Amalgam: Karies Sederhana</option>
                                <option value="02.02">Tambalan Amalgam: Karies Kompleks</option>
                                <option value="02.05">Tambalan GIC (Kaca Ionomer)</option>
                                <option value="02.08">Tambalan Resin Komposit / Akrilik</option>
                                <option value="02.09">Rekonstruksi Gigi Depan</option>
                            </optgroup>
                            <optgroup label="Endodontik / Saluran Akar">
                                <option value="03.01">Perawatan Saluran Akar (Gigi Tunggal)</option>
                                <option value="03.02">Perawatan Saluran Akar (Gigi Majemuk)</option>
                                <option value="03.05">Pulpektomi Sebagian</option>
                            </optgroup>
                            <optgroup label="Prostodontik / Gigi Tiruan">
                                <option value="04.01.01">Inlay / Onlay Sederhana</option>
                                <option value="04.01.04">Mahkota Logam Tuang</option>
                                <option value="04.01.11">Mahkota Akrilik</option>
                                <option value="04.01.13">Mahkota Porselen-Logam (PFM)</option>
                                <option value="04.02.01">Gigi Palsu Akrilik (Maks 4 Gigi)</option>
                            </optgroup>
                            <optgroup label="Bedah Mulut / Ekstraksi">
                                <option value="10.01">Pencabutan Gigi (Ekstraksi)</option>
                                <option value="10.09">Operasi Gigi Bungsu (Impaksi)</option>
                                <option value="10.12">Apeksektomi</option>
                            </optgroup>
                            <optgroup label="Pencegahan / Periodontik">
                                <option value="05.01">Pembersihan Karang Gigi (Scaling)</option>
                                <option value="05.05">Aplikasi Sealant Gigi</option>
                                <option value="08.02">Perawatan Radang Gusi (Gingivitis)</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="mb-2">
                        <span class="block text-sm font-medium text-gray-700 mb-2">Tandai Sebagai:</span>
                        <div class="space-y-3">
                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <input type="radio" name="status-perawatan" id="chkAnterior" value="anterior"
                                    class="form-radio h-5 w-5 text-gray-600 focus:ring-gray-500">
                                <div
                                    class="w-6 h-6 rounded bg-gray-400 border border-gray-500 group-hover:scale-110 transition-transform">
                                </div>
                                <span class="text-gray-700 font-medium">Perawatan Sebelumnya (Riwayat)</span>
                            </label>

                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <input type="radio" name="status-perawatan" id="chkNuevo" value="baru" checked
                                    class="form-radio h-5 w-5 text-green-500 focus:ring-green-500">
                                <div
                                    class="w-6 h-6 rounded bg-green-500 border border-green-600 group-hover:scale-110 transition-transform">
                                </div>
                                <span class="text-gray-700 font-medium">Perawatan Baru (Rencana)</span>
                            </label>

                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <input type="radio" name="status-perawatan" id="chkHapus" value="hapus"
                                    class="form-radio h-5 w-5 text-red-500 focus:ring-red-500">
                                <div
                                    class="w-6 h-6 rounded bg-white border-2 border-red-500 group-hover:scale-110 transition-transform flex items-center justify-center">
                                    <span class="text-red-500 text-xs font-bold">X</span>
                                </div>
                                <span class="text-gray-700 font-medium">Hapus Tanda / Bersihkan</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Pengaturan Warna
            const COLOR_LAPIZ = '#1e3a8a'; // Tailwind blue-900 (pengganti 'navy')
            const COLOR_HOVER = '#fef08a'; // Tailwind yellow-200
            const COLOR_ANTERIOR = '#9ca3af'; // Tailwind gray-400
            const COLOR_NUEVO = '#22c55e'; // Tailwind green-500
            const COLOR_DEFAULT = 'white';

            // Elemen DOM
            const polygons = document.querySelectorAll('polygon');
            const texts = document.querySelectorAll('text');
            const numGigiEl = document.getElementById('piezanumero');
            const sisiGigiEl = document.getElementById('piezacara');
            const statusMsgEl = document.getElementById('status-message');

            // Set warna dasar garis diagram
            polygons.forEach(p => {
                p.setAttribute('stroke', COLOR_LAPIZ);
                // Simpan state warna aslinya di dataset
                p.dataset.currentColor = COLOR_DEFAULT;
            });
            texts.forEach(t => {
                t.setAttribute('stroke', COLOR_LAPIZ);
                t.setAttribute('fill', COLOR_LAPIZ);
            });

            // Fungsi untuk menampilkan pesan status singkat
            function showStatus(msg) {
                statusMsgEl.textContent = msg;
                statusMsgEl.classList.remove('opacity-0');
                setTimeout(() => {
                    statusMsgEl.classList.add('opacity-0');
                }, 2000);
            }

            // Event Listener untuk polygon (permukaan gigi)
            polygons.forEach(sector => {
                // Saat mouse masuk (Hover)
                sector.addEventListener('mouseenter', (evt) => {
                    const el = evt.target;
                    const cara = el.getAttribute('id'); // T, B, L, R, C
                    const diente = el.parentElement.getAttribute('id').replace('P', ''); // Angka gigi

                    numGigiEl.textContent = diente;
                    sisiGigiEl.textContent = cara;

                    // Simpan warna yang sedang aktif agar bisa dikembalikan saat mouseout
                    if (!el.dataset.isHovered) {
                        el.dataset.currentColor = el.getAttribute('fill');
                    }
                    el.dataset.isHovered = 'true';
                    el.setAttribute('fill', COLOR_HOVER);
                });

                // Saat mouse keluar
                sector.addEventListener('mouseleave', (evt) => {
                    const el = evt.target;
                    el.dataset.isHovered = 'false';
                    // Kembalikan ke warna sebelum dihover
                    el.setAttribute('fill', el.dataset.currentColor);

                    numGigiEl.textContent = '--';
                    sisiGigiEl.textContent = '-';
                });

                // Saat diklik (Menerapkan warna perawatan)
                sector.addEventListener('click', (evt) => {
                    const el = evt.target;
                    const cara = el.getAttribute('id');
                    const diente = el.parentElement.getAttribute('id').replace('P', '');

                    // Cek radio button mana yang aktif
                    const radioAnterior = document.getElementById('chkAnterior');
                    const radioNuevo = document.getElementById('chkNuevo');
                    const radioHapus = document.getElementById('chkHapus');

                    let newColor = COLOR_DEFAULT;
                    let actionText = "Dibersihkan";

                    if (radioAnterior.checked) {
                        newColor = COLOR_ANTERIOR;
                        actionText = "Riwayat Ditambahkan";
                    } else if (radioNuevo.checked) {
                        newColor = COLOR_NUEVO;
                        actionText = "Perawatan Ditambahkan";
                    }

                    // Terapkan warna
                    el.dataset.currentColor = newColor; // Perbarui memori warna
                    el.setAttribute('fill', newColor);  // Tetapkan secara visual (akan ditimpa hover kuning sesaat, tapi akan kembali ke warna ini saat mouseleave)

                    // Notifikasi visual kecil
                    showStatus(`Gigi ${diente} Sisi ${cara}: ${actionText}`);
                });
            });
        });
    </script>
</body>

</html>