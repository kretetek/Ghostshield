<?php

require_once("lib/page.class.php");

$page = new page();

$page->addCSS("nanotechnology.css");

echo $page->header("Nanotechnology");

echo '<section id="full_page_content" class="white">
<link rel="stylesheet" href="https://use.typekit.net/jcf0qmg.css">
<div class="wrapper">
        <section class="header-image site-section">
            <div class="full-height">
                <div class="text-container text-center">
					<h1 class="centered">Science and Protection at the Nanoscale</h1>
					<hr/>
                    <p class="mx-10">In the spirit of scientific transparency, take a look at 3800x magnification and
                            see what drives Ghostshield; the next generation of penetrating concrete sealers and coatings.</p>
                </div>
            </div>
        </section>

        <section class="nano-section site-section bg-dark">
                <div class="text-container-centered p-10 text-center text-white">
					<h1 class="text-white centered">Nanotechnology</h1>
					<hr/>
                    <p class="mx-10">Developed over decades by research scientist and chemist Ghostshield’s formulas
                            are the most innovative concrete sealers available in the market. Utilizing molecular
                            nanotechnology, the active ingredients are engineered to have the smallest atomic structures
                            possible. The active particle sizes are 0.3 to 1.5 nanometers in diameter 100 times smaller
                            than traditional chemistries resulting in deeper penetration, longer service life and unrivaled
                            performance.</p>
                    <div class="nano-equation">
                        <div class="formula-container">
                            <div>Siliconates</div>
                            <div>(0.3 to 0.6 nm)</div>
                            <div><img src="https://images.ctfassets.net/muyees5bu8n0/FWVLgDjd2TWrsEpfXw9YY/60bf6bc578d104d8c3016d4ddb276987/siliconate.svg" alt=""></div>
                        </div>
                        <div class="formula-container">
                            <div>Silanes</div>
                            <div>(0.4 to 1.5 nm)</div>
                            <div><img src="https://images.ctfassets.net/muyees5bu8n0/sSP4GHzS343HwWplr0iNK/1f80fe695ef2ec6cea159c9ebec04c37/silane.svg" alt=""></div>
                        </div>
                        <div class="formula-container">
                            <div>Siloxanes</div>
                            <div>(3 to 30 nm)</div>
                            <div><img src="https://images.ctfassets.net/muyees5bu8n0/9Ai6eGallUh0psPWEB0oD/7d8f1ffe56e70d02a960ca2ea0d9b115/siloxane.svg" alt=""></div>
                        </div>
                        <div class="formula-container">
                            <div>Sodium Silicate</div>
                            <div>(2 to 500 nm)</div>
                            <div><img src="https://images.ctfassets.net/muyees5bu8n0/5L6gykCwdsnmdvfxsNgpvX/ff1eadd77c0f1bb5627998ffdc848b6d/sodium-silicate.svg" alt=""></div>
                        </div>
                        <div class="formula-container">
                            <div>Lithium Silicate</div>
                            <div>(0.8 to 150 nm)</div>
                            <div><img src="https://images.ctfassets.net/muyees5bu8n0/1FUYiqsryGEBDD1fPwznaI/1e810ec429d56ee0a6331d29311dea68/lithium-silicate.svg" alt=""></div>
                        </div>
                    </div>
                </div>
        </section>

        <section id="product-section-1 site-section">
            <div class="full-height">
                    <div class="nested-text">
                        <div class="nano-text-container">
                            <h1>How small is a nanometer?</h1>
                            <p> In the International System of Units, the prefix "nano" means one-billionth, or 10-9;
                                therefore one nanometer is one-billionth of a meter. It’s difficult to imagine just how
                                small that is, so here are some examples.</p>
                            <p>Unlike traditional silane / siloxane particles, on a microscopic scale, Ghostshield’s
                                nano-particles are so small they can travel through a single grain of sand, one of the
                                three crucial components of concrete worldwide.</p>
                        </div>
                        <div class="nano-image-container">
                            <div class="info-card">
                                <div class="nano-sand"></div>
                                <div class="card-text">A single grain of sand is 500,000 nanometers in diameter.</div>
                            </div>
                            <div class="info-card">
                                <div class="nano-dna"></div>
                                <div class="card-text">A strand of human DNA is 2.5 nanometers in diameter.</div>
                            </div>
                            <div class="info-card">
                                <div class="nano-cell"></div>
                                <div class="card-text">The active particle size in Ghostshield formulas is 0.3 to 1.5 nanometers in diamater.</div>
                            </div>
                        </div>
                        <div class="svg-ruler"></div>
                    </div>
            </div>
        </section>

        <section class="micro-section bg-dark site-section">
            <div class="full-height">
                <div class="text-container-centered text-center text-white">
                    <div class="text-container-centered px-10 pb-5 micro-sect-container">
						<h1 class="text-white centered">Under the Microscope</h1>
						<hr/>
                        <p class="mx-10">At 2500x magnification under the microscope, we can see the impact of Ghostshield\'s
                             nanoparticles; the covalent bond from the chemical reaction and the formation of additional CSH
                              calcium silicate hydrate within the pores. Compared to the upper untreated section of the sample,
                               this treatment observed in the lower treated section of the sample significantly reduces the
                                porosity of the concrete, increasing its mass and density making it up to .45 stronger and reducing 
                                its ability for capillary uptake and other deleterious harmful substances.</p>
                    </div>
                    <div class="image-grid microscope-pics">
                        <div id="micro-1">
                            <div class="micro-text-container">
                                <div>
                                    <span>01</span>
                                    <hr>
                                </div>
                                <div class="description-text">
                                    <span class="micro-heading">100x Magnification</span>
                                    <p>At 100x magnification under the microscope we can observe the porosity of
                                        concrete with a mix design of 4000 psi.</p>
                                </div>
                            </div>
                        </div>
                        <div id="micro-2">
                            <div class="micro-text-container">
                                <div>
                                    <span>02</span>
                                    <hr>
                                </div>
                                <div class="description-text">
                                    <span class="micro-heading">3800x Magnification</span>
                                    <p>At 3800x magnification we can observe a microscopic hairline crack within a
                                        pore wall of the 4000 psi concrete.</p>
                                </div>
                            </div>
                        </div>
                        <div id="micro-3">
                            <div class="micro-text-container">
                                <div>
                                    <span>03</span>
                                    <hr>
                                </div>
                                <div class="description-text">
                                    <span class="micro-heading">2500x Magnification</span>
                                    <p> At 2500x magnification under the microscope, we can see the impact of
                                        Ghostshield\'s nanoparticles.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="micro-vid">
            <div class="text-container text-center">
					<h1 class="centered">Behind the Lens</h1>
					<hr/>
                    <p class="mx-10">See how the high-magnification photos of the concrete were taken utilizing a SEM (Scanning Electron Microscope).</p>
                </div>
            
            <div class="video-container">
                <!--The video -->
                <video autoplay muted playsinline loop>
                  <source src="https://videos.ctfassets.net/muyees5bu8n0/2BIfy0umk5x6GHx1qJejai/7f7d704654f777e592901c6b2dabf92f/Micro_Vid_Opt_GS.mp4"
                    type="video/mp4">
                </video>
            </div>
        </section>
        <section id="voc-section" class="site-section">
            <div class="full-height bg-dark">
                <div class="grid-half">
                    <div class="voc-text-container text-white">
                        <dv class="headline">
                            <h1 class="text-white">Smaller Particles<br/>Penetrate Deeper</h1>
                            <p class="mb-5">When it comes to penetrating concrete sealer, size matters. Ghostshield\'s formulas utilize the latest advances in nanotechnology and chemistry to penetrate deeper and last longer.</p></p>
                            <!-- <a href="" slide-order="0" class="btn btn-light carousel-btn">Slide 1</a>
                            <a href="" slide-order="1" class="btn btn-light carousel-btn">Slide 2</a>
                            <a href="" slide-order="2" class="btn btn-light carousel-btn">Slide 3</a> -->
                    </div>
                    <div class="graph-half half-2">
                        <div class="graph-container">
                            <h2 class="graph-title centered">Depth of Penetration: Ghostshield\'s  Nano Particles Vs. Traditional Silane/Siloxane Chemistry</h2>
                                <img src="https://images.ctfassets.net/muyees5bu8n0/2boL9GOsNyD7BJs24B1VNt/d5e42371a61f743797ebe6d56151fb26/Penetration_Depth_Graph_V3.svg" alt="">
                            <!-- <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                                <ol class="carousel-indicators">
                                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                                    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                                </ol>
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                    <img src="Penetration_Depth_Graph.svg" alt="">
                                    </div>
                                    <div class="carousel-item">
                                    <img src="https://ghostshieldconcretesealers.comhttps://ghostshieldconcretesealers.com/wp-content/uploads/2019/01/graph1.png" alt="">
                                    </div>
                                    <div class="carousel-item">
                                    <img src="https://ghostshieldconcretesealers.comhttps://ghostshieldconcretesealers.com/wp-content/uploads/2019/01/graph1.png" alt="">
                                    </div>
                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="voc-section-2" class="site-section">
                <div class="full-height">
                    <div class="grid-half">
                        <div class="voc-text-container">
                            <dv class="headline">
                                <h1>Water-Absorption<br/>Reduction</h1>
                                <p class="mb-5">All damage to concrete requires water. Ghostshield concrete sealers aid in the prevention of freeze thaw damage, alkali-silica reaction, chloride-induced damage, surface spalling, pitting and ettringite formation extending its service life. Treated concrete lasts longer.</p>
                        </div>
                        <div class="graph-half">
                            <div class="graph-container">
                                <h2 class="graph-title centered text-black">Percentage Improvement Vs. Control</h2>
                                <img class="bar-graph" src="https://images.ctfassets.net/muyees5bu8n0/6oelc9exGvfO6mKRiS7MYy/9dc7dace486046fa89aea9259cdd38ad/Water_Absorption_Reduction_Graph_V2.svg" alt="graph">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
    </div>
	</section>

	<div id="page_footer_before"></div>';

echo $page->footer();

?>