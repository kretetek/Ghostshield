<?php

if (!isset($products)) {

	$included = false;

	if (!isset($_REQUEST["ajax"])) {
		require_once("inc/header.php");
	} else {
		require_once "../lib/sys/cms_data.php";
		$products = get_cache_data("products");
		$global_vars = get_cache_data("globals");
		$formattedData = get_cache_data("formattedData");
		foreach($global_vars as $v) {
			$GLOBALS[$v["key"]] = $v["text"];
		}
	}

} else $included = true;

$product_names = [
	'9500' => 'Lithi-Tek LS 9500',
	'8550' => 'Siloxa-Tek 8550',
	'8505' => 'Siloxa-Tek 8505',
	'8500' => 'Siloxa-Tek 8500',
	'5505' => 'Cryli-Tek 5505',
	'5500' => 'Cryli-Tek 5500',
	'5105' => 'Gem-Tek 5105',
	'5100' => 'Gem-Tek 5100',
	'4500' => 'Lithi-Tek 4500',
	'3500' => 'Sila-Tek 3500',
	'770' =>  'Countertop 770',
	'745' =>  'Polyaspartic 745',
	'660' =>  'Countertop 660',
	'645' =>  'Urethane 645',
	'325' =>  'Epoxy 325'
];

$finder = [
	'question' => 'What would you like to seal?',
	'answers' => [
		'Basement' => [
			'question' => 'Would you like to seal a wall or a floor?',
			'answers' => [
				'Basement Wall' => [
					'question' => 'Which substrate is your wall comprised of?',
					'answers' => [
						'Cinderblock' => [
							"product" => 8500,
							"product_note" => "Product Note Here"
						],
						'Poured Concrete' => [
							'question' => 'Are you preventatively sealing your floor or do you have an existing moisture or water intrusion issue?',
							'answers' => [
								'Existing Moisture' => [
									"primer" => 4500,
									"primer_note" => "Primer Note Here",
									"product" => 8500,
									"product_note" => "Product Note Here"
								],
								'Preventative' => [
									"question" => 'Are you looking for a cost-efficient solution or the best protection?',
									'answers' => [
										'Best Protection' => [
											'primer' => 4500,
											"primer_note" => "Primer Note Here",
											'product' => 8500,
											"product_note" => "Product Note Here"
										],
										'Cost Efficient' => [
											'product' => 9500,
											"product_note" => "Product Note Here"
										]
									]
								]
							]
						]
					]
				],
				'Basement Floor' => [
					'question' => 'Are you preventatively sealing your floor or do you have an existing moisture or water intrusion issue?',
					'answers' => [
						'Existing Moisture' => [
							'primer' => 4500,
							"primer_note" => "Primer Note Here",
							'product' => 8500,
							"product_note" => "Product Note Here"
						],
						'Preventative' => [
							"question" => 'Are you looking for a cost-efficient solution or the best protection?',
							'answers' => [
								'Best Protection' => [
									'primer' => 4500,
									"primer_note" => "Primer Note Here",
									'product' => 8500,
									"product_note" => "Product Note Here"
								],
								'Cost Efficient' => [
									'product' => 9500,
									"product_note" => "Product Note Here"
								]
							]
						]
					]
				]
			]
		],
		'Countertop' => [
			'question' => 'What type of Finish are you looking for?',
			'answers' => [
				'Clear' => [
					'product' => 770,
					"product_note" => "Product Note Here"
				],
				'Low Sheen' => [
					'product' => 660,
					"product_note" => "Product Note Here"
				],
				'High Gloss' => [
					'product' => 745,
					"product_note" => "Product Note Here"
				]
			]
		],
		'Driveway Sidewalk' => [
			'question' => 'What type of substrate is your driveway/sidewalk?',
			'answers' => [
				'Brick' => [
					'question' => 'Are you looking for oil or stain protection?',
					'answers' => [
						'Yes' => [
							'product' => 8505,
							"product_note" => "Product Note Here"
						],
						'No' => [
							'product' => 8500,
							"product_note" => "Product Note Here"
						]
					]
				],
				'Concrete' => [
					'question' => 'Are you looking for oil or stain protection?',
					'answers' => [
						'Yes' => [
							'product' => 8505,
							"product_note" => "Product Note Here"
						],
						'No' => [
							'question' => 'Do you live in an area that uses deicing salts on roadways?',
							'answers' => [
								'Yes' => [
									'product' => 8500,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 9500,
									"product_note" => "Product Note Here"
								]
							]
						]
					]
				],
				'Exposed Aggregate' => [
					'question' => 'What type of finish are you looking for?',
					'answers' => [
						'Clear (Natural)' => [
							'question' => 'Are you looking for oil or stain protection?',
							'answers' => [
								'Yes' => [
									'product' => 8505,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'question' => 'Do you live in an area that uses deicing salts on roadways?',
									'answers' => [
										'Yes' => [
											'product' => 8500,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 9500,
											"product_note" => "Product Note Here"
										]
									]
								]
							]
						],
						'Low Sheen' => [
							'product' => 5500,
							"product_note" => "Product Note Here"
						],
						'High Gloss' => [
							'product' => 5505,
							"product_note" => "Product Note Here"
						]
					]
				],
				'Pavers' => [
					'question' => 'What type of finish are you looking for?',
					'answers' => [
						'Clear (Natural)' => [
							'question' => 'Are you looking for oil or stain protection?',
							'answers' => [
								'Yes' => [
									'product' => 8505,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'question' => 'Do you live in an area that uses deicing salts on roadways?',
									'answers' => [
										'Yes' => [
											'product' => 8500,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 9500,
											"product_note" => "Product Note Here"
										]
									]
								]
							]
						],
						'Low Sheen' => [
							'product' => 5500,
							"product_note" => "Product Note Here"
						],
						'High Gloss' => [
							'product' => 5505,
							"product_note" => "Product Note Here"
						]
					]
				],
				'Stamped Concrete' => [
					'question' => 'What type of finish are you looking for?',
					'answers' => [
						'Clear (Natural)' => [
							'question' => 'Are you looking for oil or stain protection?',
							'answers' => [
								'Yes' => [
									'product' => 8505,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'question' => 'Do you live in an area that uses deicing salts on roadways?',
									'answers' => [
										'Yes' => [
											'product' => 8500,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 9500,
											"product_note" => "Product Note Here"
										]
									]
								]
							]
						],
						'Low Sheen' => [
							'product' => 5500,
							"product_note" => "Product Note Here"
						],
						'High Gloss' => [
							'product' => 5505,
							"product_note" => "Product Note Here"
						]
					]
				]
			]
		],
		'Garage' => [
			'question' => 'Are you looking for a clear penetrating sealer or an industrial grade gloss coating?',
			'answers' => [
				'Penetrating Sealer' => [
					'question' => 'Are you looking for oil or stain protection?',
					'answers' => [
						'Yes' => [
							'product' => 8505,
							"product_note" => "Product Note Here"
						],
						'No' => [
							'question' => 'Do you live in an area that uses deicing salts on roadways?',
							'answers' => [
								'Yes' => [
									'product' => 8500,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 9500,
									"product_note" => "Product Note Here"
								]
							]
						]
					]
				],
				'Gloss Coating' => [
					'question' => 'Are you willing to spend more for a quicker cure time?',
					'answers' => [
						'Yes' => [
							"primer" => 325,
							"primer_note" => "Primer Note Here",
							"product" => 745,
							"product_note" => "Product Note Here"
						],
						'No' => [
							"primer" => 325,
							"primer_note" => "Primer Note Here",
							"product" => 645,
							"product_note" => "Product Note Here"
						]
					]
				]
			]
		],
		'Patio' => [
			'question' => 'What substrate is your patio comprised of?',
			'answers' => [
				'Brick' => [
					'question' => 'Are you looking for oil or stain protection?',
					'answers' => [
						'Yes' => [
							'product' => 8505,
							"product_note" => "Product Note Here"
						],
						'No' => [
							'product' => 8500,
							"product_note" => "Product Note Here"
						]
					]
				],
				'Concrete' => [
					'question' => 'What type of finish are you looking for?',
					'answers' => [
						'Clear (Natural)' => [
							'question' => 'Are you looking for oil or stain protection?',
							'answers' => [
								'Yes' => [
									'product' => 8505,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 8500,
									"product_note" => "Product Note Here"
								]
							]
						],
						'Low Sheen' => [
							'product' => 5500,
							"product_note" => "Product Note Here"
						],
						'High Gloss' => [
							'product' => 5505,
							"product_note" => "Product Note Here"
						]
					]
				],
				'Exposed Aggregate' => [
					'question' => 'What type of finish are you looking for?',
					'answers' => [
						'Clear (Natural)' => [
							'question' => 'Are you looking for oil or stain protection?',
							'answers' => [
								'Yes' => [
									'product' => 8505,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 8500,
									"product_note" => "Product Note Here"
								]
							]
						],
						'Low Sheen' => [
							'product' => 5500,
							"product_note" => "Product Note Here"
						],
						'High Gloss' => [
							'product' => 5505,
							"product_note" => "Product Note Here"
						]
					]
				],
				'Paver' => [
					'question' => 'What type of finish are you looking for?',
					'answers' => [
						'Clear (Natural)' => [
							'question' => 'Are you looking for oil or stain protection?',
							'answers' => [
								'Yes' => [
									'product' => 8505,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 8500,
									"product_note" => "Product Note Here"
								]
							]
						],
						'Low Sheen' => [
							'product' => 5500,
							"product_note" => "Product Note Here"
						],
						'High Gloss' => [
							'product' => 5505,
							"product_note" => "Product Note Here"
						]
					]
				],
				'Stamped Concrete' => [
					'question' => 'What type of finish are you looking for?',
					'answers' => [
						'Clear (Natural)' => [
							'question' => 'Are you looking for oil or stain protection?',
							'answers' => [
								'Yes' => [
									'product' => 8505,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 8500,
									"product_note" => "Product Note Here"
								]
							]
						],
						'Low Sheen' => [
							'product' => 5500,
							"product_note" => "Product Note Here"
						],
						'High Gloss' => [
							'product' => 5505,
							"product_note" => "Product Note Here"
						]
					]
				],
				'Slate' => [
					'question' => 'What type of finish are you looking for?',
					'answers' => [
						'Clear (Natural)' => [
							'question' => 'Are you looking for oil or stain protection?',
							'answers' => [
								'Yes' => [
									'product' => 8505,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 8500,
									"product_note" => "Product Note Here"
								]
							]
						],
						'Low Sheen' => [
							'question' => 'Are you looking for color enhancement?',
							'answers' => [
								'Yes' => [
									'product' => 5100,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 5500,
									"product_note" => "Product Note Here"
								]
							]
						],
						'High Gloss' => [
							'question' => 'Are you looking for color enhancement?',
							'answers' => [
								'Yes' => [
									'product' => 5105,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 5505,
									"product_note" => "Product Note Here"
								]
							]
						]
					]
				],
				'Stone' => [
					'question' => 'What type of finish are you looking for?',
					'answers' => [
						'Clear (Natural)' => [
							'question' => 'Are you looking for oil or stain protection?',
							'answers' => [
								'Yes' => [
									'product' => 8505,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 8500,
									"product_note" => "Product Note Here"
								]
							]
						],
						'Low Sheen' => [
							'question' => 'Are you looking for color enhancement?',
							'answers' => [
								'Yes' => [
									'product' => 5100,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 5500,
									"product_note" => "Product Note Here"
								]
							]
						],
						'High Gloss' => [
							'question' => 'Are you looking for color enhancement?',
							'answers' => [
								'Yes' => [
									'product' => 5105,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 5505,
									"product_note" => "Product Note Here"
								]
							]
						]
					]
				],
				'Satillo Tile' => [
					'question' => 'What type of finish are you looking for?',
					'answers' => [
						'Clear (Natural)' => [
							'question' => 'Are you looking for oil or stain protection?',
							'answers' => [
								'Yes' => [
									'product' => 8505,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 8500,
									"product_note" => "Product Note Here"
								]
							]
						],
						'Low Sheen' => [
							'question' => 'Are you looking for color enhancement?',
							'answers' => [
								'Yes' => [
									'product' => 5100,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 5500,
									"product_note" => "Product Note Here"
								]
							]
						],
						'High Gloss' => [
							'question' => 'Are you looking for color enhancement?',
							'answers' => [
								'Yes' => [
									'product' => 5105,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 5505,
									"product_note" => "Product Note Here"
								]
							]
						]
					]
				]
			]
		],
		'Pool Deck' => [
			'question' => 'What type of pool do you have?',
			'answers' => [
				'Salt Water' => [
					'question' => 'Are you looking for oil or stain protection?',
					'answers' => [
						'Yes' => [
							'product' => 8505,
							"product_note" => "Product Note Here"
						],
						'No' => [
							'product' => 8500,
							"product_note" => "Product Note Here"
						]
					]
				],
				'Chlorinated' => [
					'question' => 'What substrate is your pool deck comprised of?',
					'answers' => [
						'Brick' => [
							'question' => 'Are you looking for oil or stain protection?',
							'answers' => [
								'Yes' => [
									'product' => 8505,
									"product_note" => "Product Note Here"
								],
								'No' => [
									'product' => 8500,
									"product_note" => "Product Note Here"
								]
							]
						],
						'Concrete' => [
							'question' => 'What type of finish are you looking for?',
							'answers' => [
								'Clear (Natural)' => [
									'question' => 'Are you looking for oil or stain protection?',
									'answers' => [
										'Yes' => [
											'product' => 8505,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 8500,
											"product_note" => "Product Note Here"
										]
									]
								],
								'Low Sheen' => [
									'product' => 5500,
									"product_note" => "Product Note Here"
								],
								'High Gloss' => [
									'product' => 5505,
									"product_note" => "Product Note Here"
								]
							]
						],
						'Exposed Aggregate' => [
							'question' => 'What type of finish are you looking for?',
							'answers' => [
								'Clear (Natural)' => [
									'question' => 'Are you looking for oil or stain protection?',
									'answers' => [
										'Yes' => [
											'product' => 8505,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 8500,
											"product_note" => "Product Note Here"
										]
									]
								],
								'Low Sheen' => [
									'product' => 5500,
									"product_note" => "Product Note Here"
								],
								'High Gloss' => [
									'product' => 5505,
									"product_note" => "Product Note Here"
								]
							]
						],
						'Paver' => [
							'question' => 'What type of finish are you looking for?',
							'answers' => [
								'Clear (Natural)' => [
									'question' => 'Are you looking for oil or stain protection?',
									'answers' => [
										'Yes' => [
											'product' => 8505,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 8500,
											"product_note" => "Product Note Here"
										]
									]
								],
								'Low Sheen' => [
									'product' => 5500,
									"product_note" => "Product Note Here"
								],
								'High Gloss' => [
									'product' => 5505,
									"product_note" => "Product Note Here"
								]
							]
						],
						'Stamped Concrete' => [
							'question' => 'What type of finish are you looking for?',
							'answers' => [
								'Clear (Natural)' => [
									'question' => 'Are you looking for oil or stain protection?',
									'answers' => [
										'Yes' => [
											'product' => 8505,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 8500,
											"product_note" => "Product Note Here"
										]
									]
								],
								'Low Sheen' => [
									'product' => 5500,
									"product_note" => "Product Note Here"
								],
								'High Gloss' => [
									'product' => 5505,
									"product_note" => "Product Note Here"
								]
							]
						],
						'Slate' => [
							'question' => 'What type of finish are you looking for?',
							'answers' => [
								'Clear (Natural)' => [
									'question' => 'Are you looking for oil or stain protection?',
									'answers' => [
										'Yes' => [
											'product' => 8505,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 8500,
											"product_note" => "Product Note Here"
										]
									]
								],
								'Low Sheen' => [
									'question' => 'Are you looking for color enhancement?',
									'answers' => [
										'Yes' => [
											'product' => 5100,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 5500,
											"product_note" => "Product Note Here"
										]
									]
								],
								'High Gloss' => [
									'question' => 'Are you looking for color enhancement?',
									'answers' => [
										'Yes' => [
											'product' => 5105,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 5505,
											"product_note" => "Product Note Here"
										]
									]
								]
							]
						],
						'Stone' => [
							'question' => 'What type of finish are you looking for?',
							'answers' => [
								'Clear (Natural)' => [
									'question' => 'Are you looking for oil or stain protection?',
									'answers' => [
										'Yes' => [
											'product' => 8505,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 8500,
											"product_note" => "Product Note Here"
										]
									]
								],
								'Low Sheen' => [
									'question' => 'Are you looking for color enhancement?',
									'answers' => [
										'Yes' => [
											'product' => 5100,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 5500,
											"product_note" => "Product Note Here"
										]
									]
								],
								'High Gloss' => [
									'question' => 'Are you looking for color enhancement?',
									'answers' => [
										'Yes' => [
											'product' => 5105,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 5505,
											"product_note" => "Product Note Here"
										]
									]
								]
							]
						],
						'Satillo Tile' => [
							'question' => 'What type of finish are you looking for?',
							'answers' => [
								'Clear (Natural)' => [
									'question' => 'Are you looking for oil or stain protection?',
									'answers' => [
										'Yes' => [
											'product' => 8505,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 8500,
											"product_note" => "Product Note Here"
										]
									]
								],
								'Low Sheen' => [
									'question' => 'Are you looking for color enhancement?',
									'answers' => [
										'Yes' => [
											'product' => 5100,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 5500,
											"product_note" => "Product Note Here"
										]
									]
								],
								'High Gloss' => [
									'question' => 'Are you looking for color enhancement?',
									'answers' => [
										'Yes' => [
											'product' => 5105,
											"product_note" => "Product Note Here"
										],
										'No' => [
											'product' => 5505,
											"product_note" => "Product Note Here"
										]
									]
								]
							]
						]
					]
				]
			]
		],
		'Warehouse' => [
			'question' => 'What type of finish are you looking for?',
			'answers' => [
				'Polished' => [
					'product' => 4500,
					"product_note" => "Product Note Here"
				],
				'Clear (Natural)' => [
					'question' => 'Are you looking for a water- or a solvent-based product?',
					'answers' => [
						'Water' => [
							"product" => 8505,
							"product_note" => "Product Note Here"
						],
						'Solvent' => [
							"product" => 8550,
							"product_note" => "Product Note Here"
						]
					]
				],
				'Gloss' => [
					'question' => 'Are you willing to spend more for a quicker cure time?',
					'answers' => [
						'Yes' => [
							"primer" => 325,
							"primer_note" => "Primer Note Here",
							"product" => 745,
							"product_note" => "Product Note Here"
						],
						'No' => [
							"primer" => 325,
							"primer_note" => "Primer Note Here",
							"product" => 645,
							"product_note" => "Product Note Here"
						]
					]
				]
			]
		],
		'Fountain' => [
			'product' => 8550,
			"product_note" => "Product Note Here"
		],
		'Chimney' => [
			'question' => 'What substrate is your chimney comprised of?',
			'answers' => [
				'Brick' => [
					"product" => 8500,
					"product_note" => "Product Note Here"
				],
				'Concrete Block' => [
					"product" => 8500,
					"product_note" => "Product Note Here"
				],
				'Stone' => [
					"product" => 8500,
					"product_note" => "Product Note Here"
				],
				'Stucco' => [
					"product" => 8500,
					"product_note" => "Product Note Here"
				],
				'Other' => [
					"product" => 8500,
					"product_note" => "Product Note Here"
				]
			]
		],
		'Shop Floor' => [
			'question' => 'What type of finish are you looking for?',
			'answers' => [
				'Clear (Natural)' => [
					"question" => 'Are you looking for a water- or a solvent-based product?',
					'answers' => [
						'Water' => [
							"product" => 8505,
							"product_note" => "Product Note Here"
						],
						'Solvent' => [
							"product" => 8550,
							"product_note" => "Product Note Here"
						]
					]
				],
				'Gloss' => [
					'question' => 'Are you willing to spend more for a quicker cure time?',
					'answers' => [
						'Yes' => [
							"primer" => 325,
							"primer_note" => "Primer Note Here",
							"product" => 745,
							"product_note" => "Product Note Here"
						],
						'No' => [
							"primer" => 325,
							"primer_note" => "Primer Note Here",
							"product" => 645,
							"product_note" => "Product Note Here"
						]
					]
				]
			]
		],
		'Roof' => [
			'question' => 'What type of roof do you have?',
			'answers' => [
				'Concrete Tiles' => [
					"product" => 8500,
					"product_note" => "Product Note Here"
				],
				'Flat Concrete' => [
					"primer" => 4500,
					"primer_note" => "Primer Note Here",
					"product" => 8550,
					"product_note" => "Product Note Here"
				]
			]
		],
		'Bridge' => [
			'question' => 'Are you looking for a water- or a solvent-based product?',
			'answers' => [
				'Water' => [
					'question' => 'Are you looking for oil or stain protection?',
					'answers' => [
						'Yes' => [
							'product' => 8505,
							"product_note" => "Product Note Here"
						],
						'No' => [
							'product' => 8500,
							"product_note" => "Product Note Here"
						]
					]
				],
				'Solvent' => [
					'product' => 8550,
					"product_note" => "Product Note Here"
				]
			]
		],
		'Wall' => [
			'question' => 'What type of substrate is the wall comprised of?',
			'answers' => [
				'Brick' => [
					'product' => 8500,
					"product_note" => "Product Note Here"
				],
				'Concrete' => [
					'product' => 8500,
					"product_note" => "Product Note Here"
				],
				'Stone' => [
					'product' => 8500,
					"product_note" => "Product Note Here"
				],
				'Stucco' => [
					'product' => 8500,
					"product_note" => "Product Note Here"
				]
			]
		],
		'Kennel' => [
			'question' => 'What type of finish are you looking for?',
			'answers' => [
				'Clear (Natural)' => [
					"question" => 'Are you looking for a water- or a solvent-based product?',
					'answers' => [
						'Water' => [
							"primer" => 4500,
							"primer_note" => "Primer Note Here",
							"product" => 8505,
							"product_note" => "Product Note Here"
						],
						'Solvent' => [
							"primer" => 4500,
							"primer_note" => "Primer Note Here",
							"product" => 8550,
							"product_note" => "Product Note Here"
						]
					]
				],
				'Gloss' => [
					"primer" => 325,
					"primer_note" => "Primer Note Here",
					"product" => 645,
					"product_note" => "Product Note Here"
				]
			]
		],
		'Hangar' => [
			'question' => 'Are you looking for a clear penetrating sealer or an industrial grade gloss coating?',
			'answers' => [
				'Penetrating Sealer' => [
					"primer" => 4500,
					"primer_note" => "Primer Note Here",
					"product" => 8550,
					"product_note" => "Product Note Here"
				],
				'Gloss Coating' => [
					"primer" => 325,
					"primer_note" => "Primer Note Here",
					"product" => 645,
					"product_note" => "Product Note Here"
				]
			]
		],
		'Other' => [
			/* 'call us' prompt */
		]
	]
];

//var_dump($formattedData["product-finder-questions"]["value"]);
if ($cms_data = /*json_decode(*/$formattedData["product-finder-questions"]["value"]/*, true)*/) {
	$finder = $cms_data;
} else {
	echo '<p>There was an error decoding Product Finder data. You may still use the Product Finder but the data may be slightly out-of-date.</p>';
}

echo json_encode($finder);

if (!$included) {

	if (!isset($_REQUEST["ajax"])) echo '
		<div id="banner">
			<div class="video_wrapper">
				<img src="assets/img/bg/polygon-concrete-short.jpg" />
			</div>
		</div>';

	if (!isset($_REQUEST["ajax"])) echo '
		<section id="product_finder" class="non_ajax">';

}

echo '
			<div class="pf_interior">
				<div class="valign animate-in">';

if (!isset($_REQUEST["pf"])) {
	echo '
					<h2 class="always_centered">Pro Line Product Finder</h2>
					<p>Not sure which of our products best fits the needs of your project?<br />
					Answer questions about your specific application for a product recommendation.</p>
		
					<div class="status"><a href="?pf" class="hex-button">Start</a></div>';

} else if (isset($_REQUEST["pf"])) {

	$answers = [];
	$query = isset($_REQUEST["ajax"])?"&ajax":"";
	$tier = $finder;
	$index = 1;
	
	for ($i = 1; $i < 11; $i++) {
		if (isset($_REQUEST["q".$i])) {
		
			$answers[$i] = $_REQUEST["q".$i];
			$query .= "&q".$i."=".$_REQUEST["q".$i];
			
			$tier = $tier["answers"][$_REQUEST["q".$i]];
			$index = $i + 1;
		
		}
	}
	
	
	if (isset($tier["question"])) {

		echo '
					<h3>Question #'.$index.'</h3>
					<p>'.$tier["question"].'</p>';
	
		echo '
					<ul class="answers">';
		
		foreach ($tier["answers"] as $answer => $tier2) {
			$file = "assets/img/icons/applications/". str_replace(" ","_",strtolower($answer)) .".svg";
			echo '
						<li><a href="?pf'.$query.'&q'.$index.'='.$answer.'"'.(file_exists($file)?' class="has_image" style="background-image:url('.$file.')"':'').'><span class="valign">'.$answer.'</span></a></li>';
		}
		
		echo '
					</ul>';
	
	} else if (isset($tier["product"])) {
	
		echo '
					<p class="prelude">We have a product recommendation for your project needs:</p>
					<h3 class="project_name">'.$answers[1].(isset($answers[2])?' ('.$answers[2].')':'').'</h3>';
		
		if (isset($tier["primer"])) echo '
					<div class="pf_product pf_primer">
						<img src="assets/img/bottle/angled/'.$tier["primer"].'.png" height="120" />
						<h3 class="product_name selected '.str_replace(" ","-",strtolower($products[$tier["primer"]]["name"])).'">'.$products[$tier["primer"]]["name"].'</h3>
						<p>'.$tier["primer_note"].'</p>
						<ul class="actions">
							<li><a href="product.php?model='.$tier["primer"].'" target="_blank"><span class="valign">'.$GLOBALS["view_button_text"].'</span></a></li>
							<li><a href="http://concretesealersolutions.com/?s=ghostshield+'.$tier["primer"].'" target="_blank"><span class="valign">'.$GLOBALS["buy_button_text"].'</span></a></li>
						</ul>
					</div>';
		
		echo '
					<div class="pf_product">
						<img src="assets/img/bottle/angled/'.$tier["product"].'.png" height="120" />
						<h3 class="product_name selected '.str_replace(" ","-",strtolower($products[$tier["product"]]["name"])).'">'.$products[$tier["product"]]["name"].'</h3>
						<p>'.$tier["product_note"].'</p>
						<ul class="actions">
							<li><a href="product.php?model='.$tier["product"].'" target="_blank"><span class="valign">'.$GLOBALS["view_button_text"].'</span></a></li>
							<li><a href="http://concretesealersolutions.com/?s=ghostshield+'.$tier["product"].'" target="_blank"><span class="valign">'.$GLOBALS["buy_button_text"].'</span></a></li>
						</ul>
					</div>';
	
	} else {
		
		if (isset($_REQUEST["q1"]) && $_REQUEST["q1"] == "Other") {
		
			// > 9am, < 5pm, M-F only
			if (date('H') > 9 && date('H') < 17 && date("N") < 6) {
				echo '
					<h3>Have a special need?</h3>
					<p>Our experts are happy to help you select the right sealer.<br />We\'re accessible until 5 PM <acronym title="Eastern Standard Time">EST</acronym>, please give us a call at <a href="tel:8555738383">(855) 573-8383</a>.</p>';
			} else {
				echo '
					<h3>Have a special need?</h3>
					<p>Our experts will be happy to help you select the right sealer during our business hours.</p>
					<p>Please give us a call at <a href="tel:8555738383">(855) 573-8383</a> Monday - Friday betwen 9 AM and 5 PM <acronym title="Eastern Standard Time">EST</acronym>.</p>';
			}
		
		} else {
			echo "
					<h3>Error!</h3>
					<p>There has been an error. Please contact us.</p>";
		}
	
	}

	if ($index >= 1) {
		
		echo '
					<ul class="pf_navigation">';
		
		if ($index > 1) echo '
						<li><a href="?pf'.preg_replace("/\&q".($index-1)."\=.*/","",$query).'" class="back">Return to Question #'.($index-1).'</a></li>';
	
		echo '
						<li><a href="?" class="restart">Start Over</a></li>
					</ul>';
		
	}

}

echo '
				</div>
			</div>';

if (!$included) {

	if (!isset($_REQUEST["ajax"])) {
		
		echo '
		</section>';
		
		require_once("inc/footer.php");
	
	}

}

?>