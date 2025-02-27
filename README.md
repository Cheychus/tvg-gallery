# TVG Image-Gallery

Eine Bilder-Galerie die als Testaufgabe für die TVG programmiert wurde
und an https://github.com/gtvherrmann/testaufgaben angelehnt ist. 

**Funktionen**
- Der Nutzer kann eine Gallery mit zwei Ordnern laden und 
über einen Header zwischen diesen hin und her schalten
- In jeden Ordner können über einen Upload Button im Footer Bilder 
hinzugefügt werden
  - Unterstützt werden: JPG, PNG, WEBP, BMP, AVIF
  - GIF und SVG werden bis 1 MB Größe unterstützt
    - Größere GIFs werden in WEBP umgewandelt
    - Größere SVGs werden abgelehnt, da GDImage keine Konvertierung ermöglicht
  - Aus Performance Gründen werden Overlay-Bilder > 1 MB in WEBP umgewandelt und 
  Bilder mit einer Auflösung > 1920px werden skaliert
- Über einen Button im Header können Bilder ausgewählt und gelöscht werden
- Die Bilder werden in einer endlos scrollenden Liste geladen 
  - Dabei werden über ein lazy-loading event nur die Bilder geladen, 
  welche zurzeit im Viewport sind
- Mit einem Klick auf ein Bild wird ein Overlay geladen, welches das Bild 
in voller Größe anzeigt
  - Über eine Button-Navigation oder über die Pfeiltasten kann das nächste 
  bzw. vorherige Bild im Overlay geladen werden
  - Bis das Bild vollständig geladen ist, wird eine Lade-Animation gezeigt
  - Bei einem weiteren Klick oder ESC wird das Overlay geschlossen

**Technologien**
- Frontend: HTML, TailwindCSS, JavaScript
- Backend: PHP, AltoRouter
- Bilderbearbeitung: GDImage


---

## Installation & Setup

### 1. Repository klonen
```bash
git clone https://github.com/Cheychus/tvg-gallery.git
cd tvg-gallery
```

### 2. Abhängigkeiten installieren

**Backend (PHP & Composer):**
```bash
composer install
```

**Frontend (Node.js & npm):**
```bash
npm install
```

### 3. Docker-Container starten
```bash
docker-compose up --build
```
### Alternativ: ### 
- Benötigt [PHP](https://www.php.net/downloads.php) >= 8.4
  - Installieren und php.ini im Installations-Ordner ersetzen mit Projekt php.ini

Dann Built-in Webserver starten:

```bash
cd .\public\
php -S localhost:8080
```

### 4. Anwendung aufrufen
**URL:** [http://localhost:8080](http://localhost:8080)


### CSS Änderungen 
Für Änderungen von TailwindCSS 
```bash
npm run watch
```
oder für minified CSS version
```bash
npm run build:production
```


---

## Projektstruktur
```
/database     # SQLite Datenbank
/public       # Frontend Dateien & Image-Uploads
/src          # Router Controller, PHP Classes & Views
```

---

## Dokumentation
- [Docker](https://docs.docker.com/)
- [TailwindCSS](https://tailwindcss.com/)
- [AltoRouter](https://github.com/dannyvankooten/AltoRouter/tree/master)

## API Spezifikation
- **GET /api/images/{id}**: Liste aller Bilder eines Ordners
- **POST /api/image/{id}**: Lädt ein Bild in einen Ordner (benötigt ein Image in $_FILE) 
- **DELETE /api/images**: Löscht alle ausgewählten Bilder


