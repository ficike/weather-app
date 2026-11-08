# Adatbázis struktúra módosítás javaslatok

<!-- ez a fájl egy nem kötelező feladat része -->

## Városok

- A betöltött adatokban Szombathely kétszer szerepel. A hibás rekordot el kell távolítani; a helyes régiója a Dunántúl (pontosabban a Nyugat-Dunántúl régió). A pontosabb elnevezés használatához új értéket kell felvenni a régiót tároló enumba.
- Mivel az adatbázisban magyar városok szerepelnek, és egy város egy régióhoz tartozik, a városnevek egyediek; ezért érdemes egyedi megszorítást tenni a `cities.name` mezőre, hogy az ilyen duplikáció ne kerülhessen be újra.

## Napi időjárási adatok

- A minimum-hőmérséklet, maximum-hőmérséklet és csapadék tárolható egy közös napi időjárási táblában, amennyiben mindhárom adat ugyanahhoz a városhoz és naphoz tartozik. Ehhez persze az is szükséges, hogy csak napi adatokat tárolunk.
- A dátum mező típusa `BIGINT` helyett legyen `DATE`, mivel napi adatokat tárolunk.
- A város és dátum együtt legyen egyedi, hogy egy városhoz egy napra csak egy napi rekord tartozhasson.
- A mérési tábla `city_id` mezője hivatkozzon a `cities.id` mezőre idegen kulccsal.

## Adatminőség és mértékegységek

- Érdemes felvenni új mértékegység oszlopot, ha a mértékegységek rekordonként változnak. Ellenkező esetben jó ahogy most van, tehát adott oszlophoz írt megjegyzés.
- A csapadék értéke nem lehet negatív, ezért erre érdemes adatbázis-szintű ellenőrzést beállítani.
- A hőmérséklet minimum és maximum viszonyát érdemes ellenőrizni: a minimum ne haladja meg a maximumot.
- A hiányzó mérési adatok kezelését egyértelműen meg kell határozni. Ha egy napi rekord valamelyik mérése hiányzik, a `NOT NULL` miatt azt nem lehet hiányzóként rögzíteni; ehhez az érintett mezőknél engedélyezni kell a `NULL`-t.
