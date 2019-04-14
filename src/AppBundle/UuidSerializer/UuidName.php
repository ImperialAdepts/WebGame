<?php

namespace AppBundle\UuidSerializer;

class UuidName
{
	public static function getPlanetName($data)
	{
		$planetUniqueUuid = implode('', $data);
		$nameCombinations = count(self::$colorNames) * count(self::$planetNames);
		$combination = 0;
		foreach (str_split($planetUniqueUuid) as $letter) {
			$combination = ($combination + 101*ord($letter)) % $nameCombinations;
		}
		$nameIndex = $combination % count(self::$planetNames);
		$colorIndex = $combination % count(self::$colorNames);
		return ucfirst(self::$colorNames[$colorIndex]) . ' ' .self::$planetNames[$nameIndex];
	}

	private static $planetNames = [
		"Actinium",
		"Aluminum",
		"Antimony",
		"Argon",
		"Barium",
		"Berkelium",
		"Beryllium",
		"Bismuth",
		"Bohrium",
		"Boron",
		"Bromine",
		"Cadmium",
		"Calcium",
		"Californium",
		"Carbon",
		"Cerium",
		"Cesium",
		"Chlorine",
		"Chromine",
		"Cobalt",
		"Copper",
		"Curium",
		"Darmstadtium",
		"Dubnium",
		"Dysprosium",
		"Einsteinium",
		"Erbium",
		"Europium",
		"Fermuim",
		"Fluorine",
		"Francium",
		"Gadolinium",
		"Gallium",
		"Germanium",
		"Gold",
		"Hafnium",
		"Hassium",
		"Helium",
		"Holmium",
		"Hydrogen",
		"Indium",
		"Iodine",
		"Iridium",
		"Iron",
		"Krypton",
		"Lanthanum",
		"Lawrencium",
		"Lead",
		"Lithium",
		"Lutetium",
		"Magnesium",
		"Manganese",
		"Meitnerium",
		"Mendelevium",
		"Mercury",
		"Molybdenum",
		"Neodymium",
		"Neon",
		"Neptunium",
		"Nickel",
		"Niobium",
		"Nitrogen",
		"Nobellium",
		"Osmium",
		"Oxygen",
		"Palladium",
		"Phosphorus",
		"Platinum",
		"Plutonium",
		"Polonium",
		"Potassium",
		"Praseodymium",
		"Promethium",
		"Protactinium",
		"Radium",
		"Radon",
		"Rhenium",
		"Rhodium",
		"Rubidium",
		"Ruthenium",
		"Rutherfordium",
		"Samarium",
		"Scandium",
		"Seaborgium",
		"Selenium",
		"Silicon",
		"Silver",
		"Sodium",
		"Strontium",
		"Sulfur",
		"Tantalum",
		"Technetium",
		"Tellurium",
		"Terbium",
		"Thallium",
		"Thorium",
		"Thulium",
		"Tin",
		"Titanium",
		"Tungsten",
		"Ununbium",
		"Ununhexium",
		"Ununoctium",
		"Ununpentium",
		"Ununquadium",
		"Ununseptium",
		"Ununtrium",
		"Ununium",
		"Uranium",
		"Vanadium",
		"Xenon",
		"Ytterbium",
		"Yttrium",
		"Zinc",
		"Zirconium",
	];

	private static $colorNames = [
		"amber",
		"amethyst",
		"apricot",
		"aqua",
		"aquamarine",
		"auburn",
		"azure",
		"beige",
		"black",
		"blue",
		"bronze",
		"brown",
		"buff",
		"burnt umber",
		"cardinal",
		"carmine",
		"celadon",
		"cerise",
		"cerulean",
		"charcoal",
		"chartreuse",
		"chocolate",
		"cinnamon",
		"color",
		"complementary",
		"copper",
		"coral",
		"cream",
		"crimson",
		"cyan",
		"dark",
		"denim",
		"desert sand",
		"ebony",
		"ecru",
		"eggplant",
		"emerald",
		"forest green",
		"fuchsia",
		"gold",
		"goldenrod",
		"gray",
		"green",
		"grey",
		"hot pink",
		"hue",
		"indigo",
		"ivory",
		"jade",
		"jet",
		"jungle green",
		"kelly green",
		"khaki",
		"lavender",
		"lemon",
		"light",
		"lilac",
		"lime",
		"magenta",
		"mahogany",
		"maroon",
		"mauve",
		"mustard",
		"navy blue",
		"ocher",
		"olive",
		"orange",
		"orchid",
		"pale",
		"pastel",
		"peach",
		"periwinkle",
		"persimmon",
		"pewter",
		"pink",
		"primary",
		"puce",
		"pumpkin",
		"purple",
		"rainbow",
		"red",
		"rose",
		"ruby",
		"russet",
		"rust",
		"saffron",
		"salmon",
		"sapphire",
		"scarlet",
		"sea green",
		"secondary",
		"sepia",
		"shade",
		"shamrock",
		"sienna",
		"silver",
		"spectrum",
		"slate",
		"steel blue",
		"tan",
		"tangerine",
		"taupe",
		"teal",
		"terracotta",
		"thistle",
		"tint",
		"tomato",
		"topaz",
		"turquoise",
		"ultramarine",
		"umber",
		"vermilion",
		"violet",
		"viridian",
		"wheat",
		"white",
		"wisteria",
		"yellow",
	];
}