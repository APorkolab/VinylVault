# Fejlesztői dokumentáció a VinylVault v.1.0.0 programhoz

### Készítette: Dr. Porkoláb Ádám

### Általános leírás

Az alkalmazás célja egy bakelit kereső alkalmazás megvalósítása, amely lehetővé teszi a felhasználók számára, hogy létrehozzanak, szerkesszenek, töröljenek és keressenek termékeket. Az alkalmazás egy egyszerű login/regisztrációs mechanizmust is tartalmaz, amely lehetővé teszi a felhasználók számára, hogy hozzáférjenek az alkalmazáshoz.
Illetve néhány minimális view-t is tartalmaz a frontenden, mely megkönnyítik a felhasználók számára a regisztrációt és a bejelentkezést.

### A fejlesztési kritériumok:

- Az alkalmazás keretrendszer nélküli ("vanilla") PHP legalább 8.0-ás verzióját használja (fejlesztés: 8.2.0 verzióval).
- Opcionális: Az alkalmazás használja a Composer-t a csomagok telepítésére.
- MVP verziót kell kialakítani.

  - Későbbiekben létre kell hozni egy egyszerű router osztályt, amelyen keresztül minden beérkező kérés fut.

  - Az adatbázis rétegét szét kell választani a Controller rétegtől, hasonlóan az MVC-hez.

- Az alkalmazás JSON API alapú felépítést használjon, és a beérkező adatokat validálja.
- Az adatbázisba mentésnél figyelni kell az SQL befecskendezés elleni védelemre.
- A műveletek sikerességét külön kell logolni.

## Backend:

### Használat:

1. Alapvetően a program egy MariaDB/MySQL adatbázist használ, amelyet a `backend/sql` mappában található `vinylvault.sql` fájl tartalmaz. A program használata előtt ezt a fájlt kell futtatni az adatbázisban.
2. A program használata előtt a `backend/.env` fájlban meg kell adni az adatbázis kapcsolat adatait.
3. A program használata előtt - ha még nem történt meg - a Composer telepítése szükséges. A Composer telepítéséhez a `https://getcomposer.org/download/` oldalon található leírásokat kell követni.
4. Első indítás előtt a `backend` mappában a `composer install` parancsot kell futtatni, hogy telepítse a szükséges csomagokat.
5. A program futtatásához szükséges egy web szerver, amely támogatja a PHP-t. A XAMPP vagy WAMP használata ajánlott, mivel ezek a programok egyben tartalmazzák a szükséges szervereket is.
6. A XAMPP vagy WAMP használata esetén a backendet a `c:\xampp\htdocs\vinylvault` mappába kell másolni, hogy elérjük a programot. A program figyeli, hogy a `vinylvault` mappában van-e, és ha nincs, akkor 404-es hibaüzenettel eldobja a kéréseket.

#### Kérések küldése:

1. A program futtatásához szükséges egy olyan alkalmazás, amely képes HTTP kéréseket küldeni. A zökkenőmentesebb használat érdekében ajánlott HTTPie (`https://httpie.io/docs/cli/installation`). A Windows esetén a `choco install httpie` parancs segítségével telepíthető, de ekkor előfeltételként a Chocolatey (`https://chocolatey.org/`) telepítés szükséges. A program használata a `httpie --help` parancs segítségével leírható.
   1. A kéréseket Postman (`https://www.postman.com/`) alkalmazással is lehet küldeni: én is úgy teszteltem.
2. Először a böngészőben a `http://localhost/register.php` címet kell megadni, hogy regisztráljunk egy új felhasználót.
3. A regisztráció után a `http://localhost/login.php` címet kell megadni, hogy bejelentkezzünk.

   Példakérés:

```http

http http://localhost/vinylvault/api/login.php username=alica password=secret

```

1. A sikeres bejelentkezés után a program visszaküldi azt az időről-időre frissülő JWT tokent, melyet a kérésekhez mellékelni kell. Ennek a tokennek a helyességét a program ellenőrzi, és ha helytelen, akkor 401-es hibát küld vissza. A token érvényessége 1 óra, ezután újra be kell jelentkezni a 3. pont szerint. Token nélkül egyetlen kérést sem fogad el a program, kivéve a regisztrációt és a bejelentkezést.

## Endpoints

#### Biztonsági megjegyzések:

- A program csak akkor fogadja el a kéréseket, ha a JWT token helyes. A token érvényessége 5 perc, ezután újra be kell jelentkezni.
- Minden felhasználó csak az általa létrehozott termékeket tudja módosítani vagy törölni.
- A product lekérdezésekhez minden felhasználó jogosult.

| URL                      | HTTP method | Auth (Bearer) | JSON válasz                     | Minta a kérés bodyjára                                                                                                                                                        |
| ------------------------ | ----------- | ------------- | ------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| /vinylvault/register.php | POST        | Y             | felhasználó API kulcsa          | `"formdata": [{"key": "username","value": "bela","type": "text"},{"key": "password","value": "secret","type": "text"},{"key": "name","value": "Bela Talbot","type": "text"}]` |
| /vinylvault/login.php    | POST        | Y             | a felhasználó tokenje           | `{"username": "mintabela", "password": "tarara"}`                                                                                                                             |
| /vinylvault/products     | GET         | Y             | minden termék                   | nem kell body                                                                                                                                                                 |
| /vinylvault/products/:id | GET         | Y             | egy termék az azonosító alapján | nem kell body                                                                                                                                                                 |
| /vinylvault/products     | POST        | Y             | új termék hozzáadása            | `{"name": "Példa termék", "description": "Ez egy példatermék.", "price":  0}`                                                                                                 |
| /vinylvault/products     | PATCH       | Y             | szerkesztett termék             | `{"name": "Termék neve frissítve": "description": "Ez egy igen remek példatermék.", "price": 22.5}`                                                                           |
| /vinylvault/products/:id | DELETE      | Y             | törölt termék                   | nem kell body                                                                                                                                                                 |

## A program futtatása

A program futtatásához szükséges egy web szerver, amely támogatja a PHP-t. A programot JSON kérések futtatásával lehet elérni. A XAMPP vagy WAMP használata ajánlott, mivel ezek a programok egyben tartalmazzák a szükséges szervereket is.

## A program működése

A program egy REST API-t valósít meg, amely a bakelitekkel kapcsolatos adatokat tárolja és kezeli.

### A program fájljai

- MariaDB adatbázis telepítő és konfigurátor: `sql/vinylvault.sql`

#### Részletesebben

Ez a kód egy MySQL adatbázis létrehozását és felépítését valósítja meg. Az adatbázis neve `vinylvault`, és három táblát tartalmaz: `products`, `user`, `refresh_token`.

Az adatbázis először ellenőrzi, hogy létezik-e már ilyen nevű adatbázis, ha igen, akkor törli.

Azután létrehozza az új `vinylvault` adatbázist, majd `admin` felhasználót hoz létre a `localhost`\-ról, `tM5nWLW2eNTYXsCk` jelszóval.

## Táblák

### users

Ez a tábla tárolja a felhasználók adatait.

#### Oszlopok

- `id`: az azonosítója a felhasználónak, ami automatikusan növekszik
- `name`: a felhasználó neve
- `username`: a felhasználó felhasználóneve
- `password_hash`: a felhasználó jelszavának hash-elése
- `api_key`: a felhasználó API kulcsa

A tábla `id` oszlopa az elsődleges kulcs, így ez egy egyedi érték lesz minden rekordban.

A `refresh_token` táblában a `user_id` oszlop hivatkozik erre a táblára, a `ON DELETE CASCADE` és `ON UPDATE CASCADE` opciókkal, így ha töröljük egy felhasználót, a hozzá tartozó frissítési tokenek is törlődnek, és ha frissítjük egy felhasználót, a hozzá tartozó frissítési tokenek is frissülnek.

A `products` táblában az `user_id` oszlop is hivatkozik erre a táblára, az `ON DELETE CASCADE` és `ON UPDATE CASCADE` opciókkal, így ha töröljük egy felhasználót, a hozzá tartozó termékek is törlődnek, és ha frissítjük egy felhasználót, a hozzá tartozó termékek is frissülnek.

Az `INSERT INTO` utasítások segítségével adatokat lehet felvinni a táblákba, az első INSERT INTO utasítás a `users` táblába, a második az `products` táblába, harmadik pedig a `refresh_token` táblába tölt fel adatokat.

#### Kulcsok

- ADD INDEX (user_id)
- ADD FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE

### products

Ez a tábla tárolja a termékek adatait. Minden termékhez tartozik egy felhasználói azonosító, ami azt jelzi, hogy melyik felhasználó adta hozzá a terméket.

#### Oszlopok

- user_id: a termék hozzáadójának felhasználói azonosítója, referencia a "user" tábla "id" oszlopára

#### Kulcsok

- ADD INDEX (user_id)
- ADD FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE

### refresh_token

Ez a tábla tárolja a felhasználók újra használható "refresh token"-jeit. Minden tokenhoz tartozik egy lejárati dátum és egy felhasználói azonosító.

#### Oszlopok

- token_hash: a token egyedi hash értéke (64 karakter hosszú, nem lehet nulla)
- expires_at: a token lejáratának dátuma, nem lehet nulla
- user_id: a token tulajdonosának felhasználói azonosítója, referencia a "user" tábla "id" oszlopára

#### Kulcsok

- PRIMARY KEY (token_hash)
- INDEX (expires_at)
- FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE

- A program egy `index.php` fájlban tárolja a kérések feldolgozásának algoritmusát.

#### Részletesebben

1.  A kód a `strict_types` deklaráció használatával szigorú típusellenőrzést alkalmaz a scriptben.
2.  Aztán beilleszt egy `bootstrap.php` fájlt a `require` utasítással.
3.  A kód azután feldolgozza az aktuális kérés URL-jét, hogy meghatározza a felhasználó által kérteni kívánt erőforrást és az azonosítót (ha van).
4.  Azután létrehoz egy új `Database` objektumot, amely kapcsolódási információkat (hoszt, név, felhasználó és jelszó) kap a .env környezeti változókból.
5.  Azután létrehoz egy új `JWTEncoder` objektumot, amely egy titkos kulcsot kap az környezeti változókból.
6.  Azután létrehoz egy új `Auth` objektumot, amely egy `UserModel` objektumot kap a `Database` objektumból és a `JWTEncoder` objektumból.
7.  Azután megnézi, hogy a felhasználó hozzáférés token hitelesítése sikeres-e. Ha nem, akkor kilép az alkalmazásból.
8.  Azután létrehoz egy új `ProductController` objektumot, amely egy `ProductModel` objektumot kap a `Database` objektumból és a felhasználó azonosítójából.
9.  Azután feldolgozza a felhasználó kérését és végrehajtja azt a `ProductController` objektumban.

- A program egy `bootstrap.php` fájlt használ, amely a konfigurációs fájlokat betölti és a környezeti változókat állítja be.

#### Részletesebben

1. A fájl elején Composer által generált autoload fájlt hívjuk be. Ennek segítségével tudjuk használni a projektünkben használt külső csomagokat, mint például a vlucas/phpdotenv csomagot.
2. A következő két sorban a PHP hibakezelőt és kivételkezelőt állítjuk be. A "ErrorHandler::handleError" és "ErrorHandler::handleException" függvényeket használjuk, amelyek a hibák és kivételek kezelését végzik.
3. A következő sorban létrehozzuk az immutable Dotenv objektumot, amely a projekt gyökérkönyvtárát használja, hogy betöltsük az alkalmazás környezeti változóit.
4. Végül pedig a HTTP fejlécet állítjuk be, hogy a kimeneti formátum json legyen és az UTF-8 kódolást használjuk.

- A program egy `Database.php` fájlt használ, amely a kapcsolatot kezeli a MySQL adatbázissal.

#### Részletesebben

1. A fájl egy Database osztályt mutat be, ami egy adatbázis kapcsolatot hoz létre a megadott konfiguráció alapján.
2. Az osztály egy konstruktort tartalmaz, ami a kapcsolat létrehozásához szükséges adatokat (host, adatbázis neve, felhasználói név, jelszó) várja paraméterként.
3. A getConnection metódus segítségével lekérdezhetjük a kapcsolatot, amennyiben még nem létezik, akkor létrehoz egyet.
4. Az adatbázis kapcsolatot a PHP PDO osztály segítségével hozzuk létre, ahol beállítjuk az hibakezelési módot (PDO::ERRMODE_EXCEPTION) és, hogy ne emuláljunk előkészített lekérdezéseket (PDO::ATTR_EMULATE_PREPARES), illetve ne stringgé alakítsuk át a lekérdezések eredményét (PDO::ATTR_STRINGIFY_FETCHES).

- A program egy `JWTEncoder.php` fájlt használ, amely a JWT tokeneket kezeli.

A JWTEncoder osztály egy JWT (JSON Web Token) kódoló/dekódoló osztály, amely segítségével lehetőség van JWT tokenek létrehozására és érvényesítésére.

Konstruktor

A konstruktor egyetlen paramétere egy `secretKey` string, amely a JWT tokenek aláírásának a kulcsa.

`public function __construct(private string $secretKey)`

encode() metódus

A `encode()` metódus egy `payload` tömböt vár paraméterül, amely a JWT tokenben szereplő adatokat tartalmazza. A metódus visszatérési értéke egy JWT token string.

`public function encode(array $payload): string`

decode() metódus

A `decode()` metódus egy JWT token stringet vár paraméterül, és visszatér egy tömbbel, amely a tokenben szereplő adatokat tartalmazza. Ha a token érvénytelen vagy lejárt, a metódus `InvalidArgumentException`, `InvalidSignatureException`, illetve `TokenExpiredException` kivételt dob.

- A program egy `UserModel.php` fájlt használ, amely a felhasználókat kezeli.

A UserModel osztály feladata a felhasználók adatainak lekérdezése az adatbázisból és azokat visszaadni egy tömb formájában.

Konstruktor

A konstruktor egy `Database` objektumot vár paraméterként, amivel a kapcsolatot az adatbázissal létrehozza.

getByIdentifier() metódus

A metódus 2 paramétert vár:

- `$identifier`: A felhasználó azonosítója (pl.: email cím, azonosító szám).
- `$identifierType` (opcionális, alapértelmezett értéke 'id'): Az azonosító típusa (pl.: 'email', 'id').

A metódus lekérdezi az adatbázisból a felhasználó adatait az azonosító alapján, és visszaadja egy tömb formájában. Ha a lekérdezés sikertelen, akkor `false` értéket ad vissza.

getData() metódus

A metódus nem vár paramétert és visszaadja a felhasználó adatait egy tömb formájában.

- A program egy `ProductModel.php` fájlt használ, amely a termékeket kezeli.

#### Részletesebben

A `ProductModel` osztály egy adatbázisban tárolt termékek kezelését végzi. A konstruktorban egy `Database` objektumot kap, amiből az adatbázis kapcsolatot nyeri.

`__construct(Database $database)`

A konstruktor, ami egy `Database` objektumot vár. Ebből nyeri az adatbázis kapcsolatot.

`getAllForUser(int $user_id): array`

Visszaadja az összes terméket az adatbázisból, rendezve a neve alapján.

`getForUser(int $user_id, string $id): array |false`

Visszaadja az adatbázisból azt a terméket, aminek az ID-ja megegyezik a paraméterként megadott `$id`\-val. Ha nem találja a terméket, false-t ad vissza.

`createForUser(int $user_id, array $data): string`

Létrehoz egy új terméket az adatbázisban a paraméterként megadott adatok alapján. A `$data` tömbnek tartalmaznia kell a termék nevét, árát, és lehetőség szerint a leírását és az "elérhetőségét" (`is_available`). Ha valamelyik mező nincs megadva, vagy nem megfelelő az értéke, akkor egy `ValidationException` kivételt dob. A létrehozott termék ID-ját adja vissza.

`updateForUser(int $user_id, string $id, array $data): int`

Módosítja az adatbázisban azt a terméket, aminek az ID-ja megegyezik a paraméterként megadott `$id`\-val. A `$data` tömbben megadhatóak a módosítani kívánt mezők értékei. A metódus visszaadja, hogy hány sort módosított.

`deleteForUser(int $user_id, string $id): int`

Törli az adatbázisból azt a terméket, aminek az ID-ja megegyezik a paraméterként megadott `$id`\-val. A metódus visszaadja, hogy hány sort törölt.

## Frontend:

A frontend egy Vue.js alkalmazás, amely a `vite` segítségével készült. A `vite` egy gyors és könnyen konfigurálható fejlesztői környezet, amely a modern webes fejlesztési technológiákat használja. A `vite` segítségével a frontend kódokat a böngészőben futtatjuk, így a fejlesztés gyors és hatékony. A `vite` a `vue` és a `vue-router` könyvtárakat is használja.

A frontend alkalmazás a `src` könyvtárban található. A `src` könyvtárban található a `main.js` fájl, amely a Vue alkalmazás belépési pontja. A `main.js` fájlban a Vue alkalmazás konfigurációját és a Vue alkalmazás komponenseit definiáljuk.

A Vue alkalmazás komponensei a `src/components` könyvtárban találhatóak. A `src/components` könyvtárban található a `App.vue` fájl, amely a Vue alkalmazás fő komponense. A `App.vue` fájlban a Vue alkalmazás komponenseit definiáljuk.

A `src/router.js` fájlban a Vue alkalmazás útvonalait definiáljuk.

A `src/store.js` fájlban a Vue alkalmazás állapotát definiáljuk.

A frontend képes CRUD műveleteket végrehajtani a backend alkalmazás felé irányuló HTTP kérések segítségével.

### Használat:

1. npm install
2. npm run dev

### A frontend alkalmazás komponensei:

#### Login.vue

A `Login.vue` komponens a felhasználó bejelentkezéséért felelős. A felhasználó bejelentkezéséhez meg kell adnia a felhasználónevét és a jelszavát. A felhasználó bejelentkezése után a felhasználó access_token nevű tokenjét a `localForage`-ban tároljuk. A `localForage`-ban tárolt felhasználó adatait a `src/store.js` fájlban kezeljük.

#### Register.vue

A `Register.vue` komponens a felhasználó regisztrációjáért felelős. A felhasználó regisztrációjához meg kell adnia a felhasználónevét, a jelszavát, és a jelszó megerősítését. A felhasználó regisztrációja után a felhasználó adatait a users SQL adatbázisban tároljuk.

#### ProductEdit.vue

A `ProductEdit.vue` komponens egy termék szerkesztéséért felelős. A termék szerkesztéséhez meg kell adnia a termék nevét, árát, és lehetőség szerint a leírását és az "elérhetőségét" (`is_available`). A termék szerkesztése után a termék adatait a products SQL adatbázisban tároljuk.

#### ProductList.vue

A `ProductList.vue` komponens a termékek listázásáért felelős. A termékek listázása után a termékek adatait a products SQL adatbázisban tároljuk.

#### NewProduct.vue

A `NewProduct.vue` komponens egy új termék létrehozásáért felelős. A termék létrehozásához meg kell adnia a termék nevét, árát, és lehetőség szerint a leírását és az "elérhetőségét" (`is_available`). A termék létrehozása után a termék adatait a products SQL adatbázisban tároljuk.

### A frontend alkalmazás útvonalai:

A frontend alkalmazás útvonalai a `src/router.js` fájlban találhatóak.

#### /login

A `/login` útvonal a `Login.vue` komponenshez tartozik. A `/login` útvonalon a felhasználó bejelentkezhet.

#### /register

A `/register` útvonal a `Register.vue` komponenshez tartozik. A `/register` útvonalon a felhasználó regisztrálhat.

#### /products

A `/products` útvonal a `ProductList.vue` komponenshez tartozik. A `/products` útvonalon a felhasználó termékeket listázhat.

#### /products/new

A `/products/new` útvonal a `NewProduct.vue` komponenshez tartozik. A `/products/new` útvonalon a felhasználó új terméket hozhat létre.

#### /products/:id

A `/products/:id/edit` útvonal a `ProductEdit.vue` komponenshez tartozik. A `/products/:id/edit` útvonalon a felhasználó egy terméket szerkeszthet.

### A frontend alkalmazás végpontjai:

Az alkalmazás végpontjaihoz Swagger dokumentáció készült. A Swagger dokumentáció elérhető a `/docs` útvonalon.
