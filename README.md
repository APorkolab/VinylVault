# Fejlesztői dokumentáció a VinylVault v.1.0.0 programhoz

## Backend:

### Általános leírás

Az alkalmazás célja egy bakelit kereső alkalmazás megvalósítása, amely lehetővé teszi a felhasználók számára, hogy létrehozzanak, szerkesszenek, töröljenek és keressenek termékeket. Az alkalmazás egy egyszerű login/regisztrációs mechanizmust is tartalmaz.

### A fejlesztési kritériumok:

- Az alkalmazás keretrendszer nélküli ("vanilla") PHP legalább 8.0-ás verzióját használja (fejlesztés: 8.2.0 verzióval).
- Opcionális: Az alkalmazás használja a Composer-t a csomagok telepítésére.
- MVP verziót kell kialakítani.

  - Későbbiekben létre kell hozni egy egyszerű router osztályt, amelyen keresztül minden beérkező kérés fut.

  - Az adatbázis rétegét szét kell választani a Controller rétegtől, hasonlóan az MVC-hez.

- Az alkalmazás JSON API alapú felépítést használjon, és a beérkező adatokat validálja.
- Az adatbázisba mentésnél figyelni kell az SQL befecskendezés elleni védelemre.
- A műveletek sikerességét külön kell logolni.

### Részletek

1.  A kód a `strict_types` deklaráció használatával szigorú típusellenőrzést alkalmaz a scriptben.
2.  Aztán beilleszt egy `bootstrap.php` fájlt a `require` utasítással. A fájl tartalmát nem mutatja, de valószínű, hogy néhány alapvető alkalmazáskonfigurációt állít be.
3.  A kód azután feldolgozza az aktuális kérés URL-jét, hogy meghatározza a felhasználó által kérteni kívánt erőforrást és az azonosítót (ha van).
4.  Azután létrehoz egy új `Database` objektumot, amely kapcsolódási információkat (hoszt, név, felhasználó és jelszó) kap az környezeti változókból.
5.  Azután létrehoz egy új `JWTEncoder` objektumot, amely egy titkos kulcsot kap az környezeti változókból.
6.  Azután létrehoz egy új `Auth` objektumot, amely egy `UserModel` objektumot kap a `Database` objektumból és a `JWTEncoder` objektumból.
7.  Azután megnézi, hogy a felhasználó hozzáférés token hitelesítése sikeres-e. Ha nem, akkor kilép az alkalmazásból.
8.  Azután létrehoz egy új `ProductController` objektumot, amely egy `ProductModel` objektumot kap a `Database` objektumból és a felhasználó azonosítójából.
9.  Azután feldolgozza a felhasználó kérését és végrehajtja azt a `ProductController` objektumban.

Ez a kód egy MySQL adatbázis létrehozását és feltöltését valósítja meg. A kód első lépésként törli az "vinylvault" nevű adatbázist, ha létezik, majd létrehozza azt újra.

Az adatbázis létrehozása után a kód engedélyezi az "admin" felhasználó számára az adatbázisban található táblák összes jogosultságának használatát a "localhost"-on, és megadja a jelszót "tM5nWLW2eNTYXsCk".

A kód továbbá létrehozza a "products", "user" és "refresh_token" nevű táblákat. A "products" tábla tartalmazza az id, neve, leírása, ára, elérhetősége és létrehozásának dátumát a termékekhez. A "user" tábla tartalmazza a felhasználók nevét, felhasználónevét, jelszót, api kulcsot. A "refresh_token" tábla tartalmazza a token_hash-t és a lejárat dátumát.

A kód végül beilleszti az adatokat a "products", "user" és "refresh_token" táblákba.
