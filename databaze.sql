create database primakavarna collate utf8_czech_ci;

CREATE Table stranka 
(
	id VARCHAR(100) PRIMARY KEY,
	titulek text,
	menu text,
	obsah TEXT,
	poradi int
);

insert into stranka SET 
	id = "uvod",
	titulek = "PrimaKavárna",
	menu = "domu",
	obsah = "...",
	poradi = 1;

	insert into stranka SET 
	id = "nabidka",
	titulek = "PrimaKavárna - Nabidka",
	menu = "Nabidka",
	obsah = "...",
	poradi = 2;

	insert into stranka SET 
	id = "galerie",
	titulek = "PrimaKavárna - Galerie",
	menu = "Galerie",
	obsah = "...",
	poradi = 3;

	insert into stranka SET 
	id = "rezervace",
	titulek = "PrimaKavárna - Rezervace",
	menu = "Rezervace",
	obsah = "...",
	poradi = 4;

	insert into stranka SET 
	id = "Kontakt",
	titulek = "PrimaKavárna - Kontakt",
	menu = "Kontakt",
	obsah = "...",
	poradi = 5;

	insert into stranka SET 
	id = "404",
	titulek = "Stránka neexistuje",
	menu = "",
	obsah = "...",
	poradi = 6;

