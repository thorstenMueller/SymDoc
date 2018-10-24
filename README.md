# SymDoc - Dokumentation der IP-Symcon Umgebung
Dieses Modul erzeugt auf Basis der IP-Symcon Struktur eine statische Dokumentation als MD-Dateien. Dabei werden verschiedene Objekt-Eigenschaften ausgelesen und dokumentiert. Durch die Nutzung von Hashtags (bspw. #Wohnzimmer) in den Objektinformationen kann die Dokumentation in logische Gruppen unterteilt werden.

> Dieses Modul wurde nach bestem Wissen und Gewissen erstellt. Der Einsatz erfolgt auf eigene Gefahr und der Autor ist nicht haftbar für eventuelle Schäden.

### Inhaltverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Installation](#3-installation)
4. [Hashtags](#3-hashtags)
5. [Doku-betrachten](#4-doku-betrachten)
6. [Screenshots](#4-screenshots)
7. [PHP Kommandos](#7php-kommandos)
8. [Todo](#4-todo)
9. [Aufmerksamkeit](#8-aufmerksamkeit)


### 1. Funktionsumfang
* Folgende Objekt-Typen werden von der Dokumentation berücksichtigt
    * Scripts
    * Variablen
    * Ereignisse
    * Medien
    * Instanzen
    * Links

* Pro Objekt wird eine Detailseite (./details/ObjectId.md) erzeugt die von der Übersichtsseite aus verlinkt wird.
* Die Übersichtsseite enthält tabellarisch folgende Informationen
    * Header
    * Allgemeine IPS Informationen
    * Erweitere IPS Einstellungen
    * Logische Gruppen pro #Tag mit Auflistung der einzelnen zugehörigen Objekttypen
    * Footer
* Defekte Scripte (broken) werden auf der Übersichtsseite durchgestrichen
* Es kann mit #Hashtags in den Objektbeschreibungen gearbeitet werden. Damit werden Objekte nach Tags gruppiert angezeigt (bspw. werden Scripte, Variablen, Instanzen, etc. #Wohnzimmer #Beleuchtung) entsprechend gruppiert angezeigt. Die Hashtags können in den tabellarischen Übersichten ausgefiltert werden.
* Über eine TextBox Variable können entweder statische oder dynamische Inhalte in den Kopf der Doku geschrieben werden. Bspw.: Erklärender Text für jemanden der die Doku sieht und vielleicht nicht mit den Details der Haustechnik vertraut ist. Alternativ kann die Variable auch durch Skriptbefüllung aktuelle Informationen die im Kopf der Doku stehen sollen anzeigen.
* Es werden einige PHP Funktionen zur Einbindung in eigene Skripte angeboten (u.a. eine Logik um rekursiv die ObjectInfo zu beschreiben): Siehe [PHP-Befehlsreferenz](#7-php-befehlsreferenz)


### 2. Voraussetzungen
* IP-Symcon Version 5

### 3. Installation

> Bei kommerzieller Nutzung (z.B. als Errichter oder Integrator) wenden Sie sich bitte an den Autor.

* Die Instanz "SymDoc" anlegen
![Instanz anlegen](./symdocAddInstance.png)
* Das Ausgabeverzeichnis der Doku angeben (der Ordner muss bereits existieren).
![Instanz konfigurieren](./symdocConfiguration.png)

### 3. Hashtags
In der Beschreibung (ObjectInfo) von Objekten (egal welchen Typs) kann neben dem Beschreibungstext noch mit Hashtags gearbeitet werden.

Durch die Nutzung von Hashtags (#) kann eine ganz individuelle Struktur aufbebaut werden.

#### Beispiel einer ObjectInfo:
> Dieses Skript schaltet die Wohnzimmerbeleuchtung bei Sonnenuntergang ein. #Beleuchtung #Todo #Visu

Auf der Übersichtsseite würde dieses Script nun logisch in drei Gruppen auftauchen (Beleuchtung, Todo und Visu). Somit kann jedes IPS Objekt beliebig vielen logischen Gruppen zugeordnet werden.

### 4. Doku erstellen
* Auf der Modul Konfigurationsseite durch Klick auf "Symcon Struktur dokumentieren".
* Per PHP Codeaufruf: SymDoc_WriteMd(Id);    

Im konfigurierten Ausgabeverzeichnis wird ein Unterverzeichnis (aktuelles Datum) angelegt. Darunter wird die Dokumentation erzeugt.

Auf der Übersichtsseite werden die abfragbaren Informationen tabellarisch aufgelistet. Zusätzlich wird das Feld "ObjectInfo" angezeigt, somit empfiehlt es sich auch (gerade bei wichtigen Objekten) einen zusätzlichen Beschreibungstext einzugeben.

### 4. Doku betrachten
Es gibt mehrere Möglichkeiten MD-Dateien zu betrachten.
* Browser Plugin (bspw. Firefox Plugin: Markdown Viewer Webext)
* Texteditor/Plugin (Atom, Notepad++, Visual Studio Code)
* Eigenständige MD-Betrachter

## 5. Screenshots
Da Bilder bekanntlich mehr sagen als Worte sind hier einige Screenshots.

### 5.1 Screenshots aus der IPS Webconsole
![](./objectTreeOverview.png)
![](./addEventWithConditions.png)
![](./addScriptBroken.png)
![](./addScriptWorking.png)
![](./addVarLogged.png)

### 5.2 Screenshots aus generierter Doku
![](./symdocOverviewHeader.png)
![](./symdocOverviewTocContent.png)
![](./symdocScript.png)
![](./symdocEvent.png)
![](./symdocOverviewUntagged.png)


### PHP Kommandos

```php
<?

$symDocInstance = 52912;

// Erzeugt die Dokumentation
SymDoc_WriteMd($symDocInstance);

// Liefert ein Array mit allen verwendeten Tags gemäß ObjectInfo
$allTags = SymDoc_ListTags($symDocInstance);
print_r($allTags);

// Schreibt für alle Objekte unterhalb von ParentId rekursiv eine ObjectInfo und hängt diese an eine bestehende ObjectInfo an.
SymDoc_WriteRecursiveObjInfo($symDocInstance,<ParentId>,"Das ist die Beschreibung. #Tag1 #Tag2", true);

?>
```

### 4. Todo
Es gibt noch einige offene Themen
* | (Pipe) Zeichen im Objektnamen maskieren, damit Tabelle nicht verhagelt wird
* Dubletten aus Referenztabellen rausfiltern
* Ereignisdetailseite  verbessern
    * (zykl. Ereignisse) komplexe Einstellungsmöglichkeiten
    * (Wochenplan) komplexe Zeitpläne
* Tabellen sortieren (nach konfigurierbarer Spalte)
* Einen PDF Export anbieten
* Export Verzeichnis auf eine Netzwerkfreigabe schreiben
* Ladekringel beim Starten der Doku aus Konfigurationsformular
* Ggf. im Script den PHP Timeout hochsetzen
* Konfigurationsformular (SelectDir statt SelectFile - gibts die Möglichkeit)
* Problem falls ModulId bei Instanzen nicht funktioniert (aktuell auskommentiert)
* Umgang mit PHP Timeouts bei größeren Umgebungen


### Aufmerksamkeit
Die Erstellung dieses Moduls hat mich ziemlich viel Zeit, Arbeit, Schweiß und vor allem Nerven gekostet!
Für die nicht kommzerielle Nutzung ist es kostenlos. Feedback (Lob, Kritik, Anregungen, etc.) ist gerne im IPS Forum gesehen


thorsten9