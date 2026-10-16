---
name: barbers-shop
description: Pretraga proizvoda, cene, dostava i poručivanje u online prodavnici Barbers (kozmetika za bradu, kosu i brijanje, Srbija).
---

# Barber Shop – vodič za AI agente

Barbers je online prodavnica kozmetike i opreme za negu brade, brkova i kose i za brijanje. Cene su u dinarima (RSD), isporuka je na teritoriji Srbije.

## Pretraga i pregled proizvoda

Koristi javni WooCommerce Store API (samo čitanje, bez autentifikacije):

- Pretraga: `GET https://barbershop.wp1.smartweb.rs/wp-json/wc/store/v1/products?search=ulje%20za%20bradu&per_page=20`
- Proizvod po ID-u: `GET https://barbershop.wp1.smartweb.rs/wp-json/wc/store/v1/products/{id}`
- Kategorije: `GET https://barbershop.wp1.smartweb.rs/wp-json/wc/store/v1/products/categories`
- Proizvodi iz kategorije: `GET https://barbershop.wp1.smartweb.rs/wp-json/wc/store/v1/products?category={slug}`

Cene u odgovoru su u najmanjoj jedinici valute (`prices.currency_minor_unit`). Polje `permalink` vodi na stranicu proizvoda.

## Glavne kategorije

- [Aksesoari](https://barbershop.wp1.smartweb.rs/kategorija/aksesoari/)
- [Brijanje](https://barbershop.wp1.smartweb.rs/kategorija/brijanje/)
- [Kozmetika za bradu](https://barbershop.wp1.smartweb.rs/kategorija/kozmetika-za-bradu/)
- [Kozmetika za kosu](https://barbershop.wp1.smartweb.rs/kategorija/kozmetika-za-kosu/)
- [Telo](https://barbershop.wp1.smartweb.rs/kategorija/telo/)

## Poručivanje

1. Otvori stranicu proizvoda (iz [prodavnice](https://barbershop.wp1.smartweb.rs/shop/) ili iz pretrage) i dodaj ga u korpu.
2. Pregledaj [korpu](https://barbershop.wp1.smartweb.rs/cart/).
3. Završi porudžbinu na [stranici za plaćanje](https://barbershop.wp1.smartweb.rs/checkout/) – kupac unosi podatke za dostavu. Porudžbinu uvek potvrđuje kupac.

## Dostava i plaćanje

- Dostava na teritoriji Srbije: od 450 do 550 RSD, u zavisnosti od lokacije.
- Za porudžbine iznad 5.000 RSD dostava je besplatna.
- Plaćanje: pouzećem (gotovinom prilikom preuzimanja).

## Kontakt

- [Kontakt stranica](https://barbershop.wp1.smartweb.rs/kontakt/)

## Dodatno

- [llms.txt](https://barbershop.wp1.smartweb.rs/llms.txt) – pregled sadržaja sajta za jezičke modele.
