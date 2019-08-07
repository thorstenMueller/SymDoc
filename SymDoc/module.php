<?php

include_once __DIR__ . '/../libs/vendor/autoload.php';


/**
  * The module "SymDoc" generated a pdf documentation of an IP-Symcon installation.
  *
  * @author Thorsten Mueller <MrThorstenM (at) gmx.net> / thorsten9
  * @since 5.1.0
  *
  * i provide this module as it is, without any kind of warranty, and without any responsibility for damages from using this module.
  *
  */
  
    // Klassendefinition
    class SymDoc extends IPSModule
    {
        private $ipsObjectType = array();
        private $ipsEventCyclingTime = array();
        private $ipsEventType = array();
        private $ipsEventTriggerType = array();
        private $ipsEventCyclicDate = array();
        private $ipsVariableType = array();
        private $ipsEventConditionVar = array();
        private $ipsEventConditionType = array();
        private $ipsInstanceStatus = array();
        private $ipsMediaType = array();
        private $ipsUtilControlId;
        private $ipsArchiveControlId;
        private $tagList = array();

        private $pdf;

        public function __construct($InstanceID)
        {
            parent::__construct($InstanceID);
        }
 
        public function Create()
        {
            parent::Create();

            $this->RegisterPropertyBoolean("overviewPrefixText", true);
            $this->RegisterPropertyBoolean("overviewGeneralInfos", true);
            $this->RegisterPropertyBoolean("overviewExtProperties", true);
            $this->RegisterPropertyBoolean("overviewScripts", true);
            $this->RegisterPropertyBoolean("overviewStrikeBrokenScripts", true);
            $this->RegisterPropertyBoolean("overviewVars", true);
            $this->RegisterPropertyBoolean("overviewLinks", true);
            $this->RegisterPropertyBoolean("overviewEvent", true);
            $this->RegisterPropertyBoolean("overviewInstances", true);
            $this->RegisterPropertyBoolean("overviewMedia", true);
            $this->RegisterPropertyBoolean("detailsScriptInclude", true);
            $this->RegisterPropertyBoolean("overviewRemoveDescTags", false);
            $this->RegisterPropertyBoolean("detailsShowRefs", false);
            $this->RegisterPropertyBoolean("detailsIncludeVarPages", true);
            $this->RegisterPropertyBoolean("detailsIncludeScriptPages", true);
            $this->RegisterPropertyBoolean("detailsIncludeEventPages", true);
            $this->RegisterPropertyBoolean("detailsIncludeInstancePages", true);
            $this->RegisterPropertyBoolean("detailsIncludeMediaPages", true);
            
            $idPrefixVar = $this->RegisterVariableString("SymDoc_PrefixText", $this->Translate("individual prefix text"), "~TextBox", 0);
            IPS_SetInfo($idPrefixVar, "Variable für individuellen Text im Kopf der erzeugten #SymDoc Doku.");
        }
 
        public function ApplyChanges()
        {
            parent::ApplyChanges();
        }

        /**
         * Set required constants including the ips required translation.
         *
         * @return void
         */
        private function setConsts()
        {
            $this->SendDebug(__FUNCTION__, "Ermittle Id vom UtilControl und Archiv", 0);
            $this->ipsUtilControlId = IPS_GetInstanceListByModuleID("{B69010EA-96D5-46DF-B885-24821B8C8DBD}")[0];
            $this->ipsArchiveControlId = IPS_GetInstanceListByModuleID("{43192F0B-135B-4CE7-A0A7-1475603F3060}")[0];
            $this->SendDebug(__FUNCTION__, "UtilControl: " . $this->ipsUtilControlId . " --- Archiv: " . $this->ipsArchiveControlId, 0);
            
            // Set object types
            $this->SendDebug(__FUNCTION__, "Setze ipsObjectType", 0);
            array_push($this->ipsObjectType, $this->Translate('category'));
            array_push($this->ipsObjectType, $this->Translate('instance'));
            array_push($this->ipsObjectType, $this->Translate('variable'));
            array_push($this->ipsObjectType, $this->Translate('script'));
            array_push($this->ipsObjectType, $this->Translate('event'));
            array_push($this->ipsObjectType, $this->Translate('media'));
            array_push($this->ipsObjectType, $this->Translate('link'));

            // Set event types
            $this->SendDebug(__FUNCTION__, "Setze ipsEventCyclingTime", 0);
            array_push($this->ipsEventCyclingTime, $this->Translate('daily'));
            array_push($this->ipsEventCyclingTime, $this->Translate('once'));
            array_push($this->ipsEventCyclingTime, $this->Translate('daily'));
            array_push($this->ipsEventCyclingTime, $this->Translate('weekly'));
            array_push($this->ipsEventCyclingTime, $this->Translate('monthly'));
            array_push($this->ipsEventCyclingTime, $this->Translate('daily'));
        
            // Set event trigger
            $this->SendDebug(__FUNCTION__, "Setze ipsEventType", 0);
            array_push($this->ipsEventType, $this->Translate('triggered'));
            array_push($this->ipsEventType, $this->Translate('cyclic'));
            array_push($this->ipsEventType, $this->Translate('weekplan'));

            // Set event trigger type details
            $this->SendDebug(__FUNCTION__, "Setze ipsEventTriggerType", 0);
            array_push($this->ipsEventTriggerType, $this->Translate('on variable update'));
            array_push($this->ipsEventTriggerType, $this->Translate('on variable change'));
            array_push($this->ipsEventTriggerType, $this->Translate('on limit drop'));
            array_push($this->ipsEventTriggerType, $this->Translate('on limit exceed'));
            array_push($this->ipsEventTriggerType, $this->Translate('on defined value'));

            // Set event cyclic details
            $this->SendDebug(__FUNCTION__, "Setze ipsEventCyclicDate", 0);
            array_push($this->ipsEventCyclicDate, $this->Translate('no date type'));
            array_push($this->ipsEventCyclicDate, $this->Translate('once'));
            array_push($this->ipsEventCyclicDate, $this->Translate('daily'));
            array_push($this->ipsEventCyclicDate, $this->Translate('weekly'));
            array_push($this->ipsEventCyclicDate, $this->Translate('monthly'));
            array_push($this->ipsEventCyclicDate, $this->Translate('yearly'));

            // Set variable types
            $this->SendDebug(__FUNCTION__, "Setze ipsVariableType", 0);
            array_push($this->ipsVariableType, "Boolean");
            array_push($this->ipsVariableType, "Integer");
            array_push($this->ipsVariableType, "Float");
            array_push($this->ipsVariableType, "String");

            // Set event conditions on vars
            $this->SendDebug(__FUNCTION__, "Setze ipsEventConditionVar", 0);
            array_push($this->ipsEventConditionVar, $this->Translate("equals"));
            array_push($this->ipsEventConditionVar, $this->Translate("unequal"));
            array_push($this->ipsEventConditionVar, $this->Translate("greater"));
            array_push($this->ipsEventConditionVar, $this->Translate("greater or equal"));
            array_push($this->ipsEventConditionVar, $this->Translate("less"));
            array_push($this->ipsEventConditionVar, $this->Translate("less or equal"));

            $this->SendDebug(__FUNCTION__, "Setze ipsEventConditionType", 0);
            array_push($this->ipsEventConditionType, $this->Translate("all conditions must apply"));
            array_push($this->ipsEventConditionType, $this->Translate("just one condition must apply"));

            $this->SendDebug(__FUNCTION__, "Setze ipsInstanceStatus", 0);
            $this->ipsInstanceStatus[101] = $this->Translate("Instance will be created");
            $this->ipsInstanceStatus[102] = $this->Translate("Instance is active");
            $this->ipsInstanceStatus[103] = $this->Translate("Instance will be deleted");
            $this->ipsInstanceStatus[104] = $this->Translate("Instance is inactiv");
            $this->ipsInstanceStatus[105] = $this->Translate("Instance not created");
        
            $this->SendDebug(__FUNCTION__, "Setze ipsMediaType", 0);
            array_push($this->ipsMediaType, $this->Translate("form"));
            array_push($this->ipsMediaType, $this->Translate("image"));
            array_push($this->ipsMediaType, $this->Translate("sound"));
            array_push($this->ipsMediaType, $this->Translate("stream"));
            array_push($this->ipsMediaType, $this->Translate("chart"));
            array_push($this->ipsMediaType, $this->Translate("document"));
        }

        private function extendMemoryLimit()
        {
            ini_set('max_execution_time', 1800);
            ini_set('memory_limit', '-1');
        }

        private function initPdf()
        {
            $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $titel = IPS_GetName(0);
            
            $this->pdf->SetAuthor("SymDoc");
            $this->pdf->SetTitle($this->Translate("Symcon documentation") . ': ' . date('d.m.Y H:i'));
        }

        private function createMediaDoc()
        {
            $docObjId = IPS_CreateMedia(5);
        
            $name = $this->Translate("Symcon documentation") . "_" . date("d.m.Y_H_i");
            IPS_SetName($docObjId, $name);
            IPS_SetParent($docObjId, $this->InstanceID);
            IPS_SetMediaFile($docObjId, "media/" . $name . ".pdf", false);
            
            return $docObjId;
        }

        /**
          * Generates a text for the top of the overview page.
          * This includes:
          * - Title (Name of root id 0)
          * - Date and time of document creation
          * - Machine name where document was created
          * - Individual content based on instance variable
          *
          * @return String the pdf formatted header text
          */
        private function overviewHeader()
        {
            $this->SendDebug(__FUNCTION__, "Starte mit der Erzeugung des Overview Headers", 0);
            $text = "<h1>" . $this->Translate("Symcon documentation" . " " . IPS_GetName(0)) . "</h1>" . PHP_EOL;
            $text .= "<h3>" . sprintf($this->Translate("this documentation was created automatically on %s for machine %s"), date("d.m.Y H:i"), gethostname()) . "</h3>";
     
            return $text;
        }
  
        /**
          * Generates an individual text if the related var has a value 
          *
          * @return String the individual start text as html
          */
        private function overviewIndividualText()
        {
            $text = "<h1><center>Individuelle Einleitung</center></h1>";
            $this->SendDebug(__FUNCTION__, "Der benutzerdefinierte Text soll im Header angezeigt werden.", 0);
            $text .= GetValue($this->GetIDForIdent("SymDoc_PrefixText")) . PHP_EOL . PHP_EOL;

            return $text;
        }


        public function WritePdf()
        {
            $this->SendDebug(__FUNCTION__, "Starte Doku Erzeugung", 0);

            $this->extendMemoryLimit();
            $this->setConsts();

            // Ouput PDF als Medium erzeugen und PDF initialisieren
            $docObjId = $this->createMediaDoc();
            $this->initPdf();
            
            // Daten für die Doku aufbereiten (IDs pro Tag)
            $this->genOverviewArray();

            // Deckblatt generieren
            $this->genDeckblatt();

            // Prüfen ob individueller Einleitungstext ausgegeben werden soll
            if ($this->ReadPropertyBoolean("overviewPrefixText") && (strlen(GetValue($this->GetIDForIdent("SymDoc_PrefixText"))) > 0)) {
                $this->pdf->AddPage();
                $this->pdf->Bookmark('Individuelle Einleitung', 0, -1, '', '', array(0,0,0));
                $this->pdf->writeHTML($this->overviewIndividualText(), true, false, true, false, 'C');
            }

            // Allgemeine IPS Informationen
            $this->pdf->Bookmark('Allgemeine IPS Informationen', 0, -1, '', 'B', array(0,0,0));
            
            // IPS Properties
            $this->pdf->AddPage();
            

            $this->pdf->Bookmark('Generische Informationen', 1, -1, '', '', array(0,0,0));
            $this->pdf->writeHTML($this->overviewGenericInfo(), true, false, true, false, '');

            // Spezialschalter
            $this->pdf->Bookmark('Erweiterte Eigenschaften', 1, -1, '', '', array(0,0,0));
            $this->pdf->writeHTML($this->overviewExtProps(), true, false, true, false, '');

            // Zusammenfassung aller Objekte pro Tag
            // =====================================
            $this->pdf->Bookmark("Zusammenfassung der Objekte", 0, -1, '', '', array(0,0,0));

            // Liste aller Tags
            $tagArray = $this->tagList;

            // Eine Seite pro "Tag" und pro "Tag" und Typ einen Eintrag ins Inhaltsverzeichnis
            foreach ($tagArray as $tagName => $value) {
                $this->SendDebug(__FUNCTION__, "Starte Tag '" . $tagName . "'", 0);

                // Jedes Tag auf einer extra Seite mit Link im Inhaltsverzeichnis
                $this->pdf->AddPage('L', 'A4', true, false);
                $this->pdf->Bookmark($tagName, 1, -1, '', '', array(0,0,0));

                // Einen Eintrag im Inhaltsverzeichnis pro Objekt-Typ (Skript, Instanz, etc.)
                $typeArray = $this->tagList[$tagName];
                foreach ($typeArray as $typeName => $value2) {
                    $this->pdf->Bookmark($typeName, 2, -1, '', '', array(0,0,0));
                    $this->pdf->writeHTML($this->genOverviewByTag($tagName, $typeName));
                }
            }

            // Start der Detailseiten
            $this->pdf->AddPage('P');
            $this->pdf->Bookmark("Objekte im Detail", 0, -1, '', '', array(0,0,0));
            
            $this->createScriptFiles();
            $this->createVariableFiles();

            // Inhaltsverzeichnis
            $this->pdf->addTOCPage('P');

            // write the TOC title
            $this->pdf->MultiCell(0, 0, 'Inhaltsverzeichnis', 0, 'L', 0, 1, '', '', true, 0);
            $this->pdf->Ln();
            
            $this->pdf->addTOC(2, 'helvetica', '.', 'INDEX', '', array(0,0,0));
            
            // end of TOC page
            $this->pdf->endTOCPage();
            
            $output = $this->pdf->Output("", "S");
            IPS_SetMediaContent($docObjId, base64_encode($output));

        }

        private function genDeckblatt()
        {
            // Deckblatt mit IPS Logo erzeugen
            $this->pdf->AddPage();
            $this->pdf->setJPEGQuality(100);

            $img = __DIR__ . '/../imgs/symcon_automation_solutions.svg';
            $this->pdf->ImageSVG($file=$img, $x=0, $y=50, $w='', $h=100, $link='', $align='N', $palign='C', $border=0, $fitonpage=true);
            $this->pdf->SetXY(1, 150, true);
            $this->pdf->SetFont('helvetica', 'B', 28);
            $this->pdf->Write('', IPS_GetName(0), '', false, 'C', true, 0, false, false, 0, 0, '');
            
            $this->pdf->SetFont('helvetica', '', 20);
            $this->pdf->Write('', gethostname(), '', false, 'C', false, 0, false, false, 0, 0, '');
            
            $this->pdf->Ln('', false);
        
            $this->pdf->Write('', 'Stand: ' . date('d.m.Y H:i'), '', false, 'C', false, 0, false, false, 0, 0, '');
            $this->pdf->SetFont('helvetica', '', 10);
        }
        
        private function overviewExtProps()
        {
            $this->SendDebug(__FUNCTION__, "Start ext. Properties", 0);

            $text = "<h1>" . $this->Translate("Symcon extended properties") . "</h1>" . PHP_EOL;
            $text .= "<table border=\"1\" cellpadding=\"2px\">";

            foreach (IPS_GetOptionList() as $key) {
                $text .= "<tr><td>" . $key . "</td><td>" . IPS_GetOption($key) . "</td></tr>" . PHP_EOL;
            }

            $text .= "</table>";

            $this->SendDebug(__FUNCTION__, "Stop ext. Properties", 0);

            return $text;
        }
        

        /**
         * Generates a table text with generic IPS information
         *
         * @return String the pdf formatted table text with generic IPS information
         */
        private function overviewGenericInfo()
        {
            $this->SendDebug(__FUNCTION__, "Erzeuge Text fuer die allgemeinen IPS Programmeinstellungen", 0);

            $text = "<h1>" . $this->Translate("Generic program information") . "</h1>" . PHP_EOL;
            $text .= "<table border=\"1\" cellpadding=\"2px\">";
       
            $text .= "<tr><td>" . $this->Translate("program directory") . "</td><td>" . IPS_GetKernelDir() . "</td></tr>" . PHP_EOL;
            $text .= "<tr><td>" . $this->Translate("platform") . "</td><td>" . IPS_GetKernelPlatform() . "</td></tr>" . PHP_EOL;
            $text .= "<tr><td>" . $this->Translate("kernel version") . "</td><td>" . IPS_GetKernelVersion() . "</td></tr>" . PHP_EOL;
            $text .= "<tr><td>" . $this->Translate("kernel revision") . "</td><td>" . IPS_GetKernelRevision() . "</td></tr>" . PHP_EOL;
            $text .= "<tr><td>" . $this->Translate("log directory") . "</td><td>" . IPS_GetLogDir() . "</td></tr>" . PHP_EOL;
            
            $this->SendDebug(__FUNCTION__, "Zeige das Ablaufdatum der Subscription an", 0);
            $subscriptionValidUntil = date("d.m.Y H:i", GetValue(IPS_GetObjectIDByIdent("LicenseSubscription", $this->ipsUtilControlId)));
            $text .= "<tr><td>" . $this->Translate("subscription valid to") . "</td><td>" . $subscriptionValidUntil . "</td></tr>" . PHP_EOL;

            $text .= "</table>";

            return $text;
        }


        /**
         * Generate overview grouped by tags within object description.
         *
         * @return String the html formatted string with table overviews of all tags and object types
         */
        private function genOverviewByTag($tagName, $typeName)
        {
            $this->SendDebug(__FUNCTION__, "Erzeuge Text fuer die Uebersichtsseite für Tag " . $tagName . " und Typ " . $typeName, 0);
            $timeStart = microtime(true);
            $text = "";
            $text .= "<h2>" . $tagName . " (" . $typeName . ")</h2>" . PHP_EOL;

            // Übersicht der Skripte
            // ---------------------
            if (($typeName == $this->Translate("SCRIPT")) && ($this->ReadPropertyBoolean("overviewScripts"))) {
                $this->SendDebug(__FUNCTION__, "Starte Tag '" . $tagName . "' (SCRIPT)", 0);
            
                $text .= "<table border=\"1\" cellpadding=\"2px\">" . PHP_EOL;
                $text .= "<thead><tr align=\"center\" style=\"font-weight:bold;background-color:#0b2f51;color:#ffffff;\">" . PHP_EOL;
                $text .= "<td width=\"10%\">Id</td>" . PHP_EOL;
                $text .= "<td width=\"35%\">" . $this->Translate("name and location") . "</td>" . PHP_EOL;
                $text .= "<td width=\"10%\">" . $this->Translate("childs") . "</td>" . PHP_EOL;
                $text .= "<td width=\"20%\">" . $this->Translate("last execution") . "</td>" . PHP_EOL;
                $text .= "<td width=\"25%\">" . $this->Translate("description") . "</td>" . PHP_EOL;
                $text .= "</tr></thead>" . PHP_EOL;

                foreach ($this->tagList[$tagName][$typeName] as $key3 => $value3) {
                    if ((IPS_GetScript($value3)['ScriptIsBroken']) && ($this->ReadPropertyBoolean("overviewStrikeBrokenScripts"))) {
                        $tmpBrokenStart = "<del>";
                        $tmpBrokenEnd = "</del>";
                    } else {
                        $tmpBrokenStart = "";
                        $tmpBrokenEnd = "";
                    }

                    $text .= "<tr align=\"center\"><td width=\"10%\">";
                    $text .= "<a href=\"#" . $value3 . "\">" . $tmpBrokenStart . $value3 . $tmpBrokenEnd . "</a></td>";

                    $text .= "<td align=\"left\" width=\"35%\">" . $tmpBrokenStart . IPS_GetLocation($value3) . $tmpBrokenEnd . "</td>";
                    $text .= "<td width=\"10%\">" . $tmpBrokenStart . count(IPS_GetChildrenIDs($value3)) . $tmpBrokenEnd . "</td>";
                    $text .= "<td width=\"20%\">" . $tmpBrokenStart . date("d.m.Y H:i", IPS_GetScript($value3)['ScriptExecuted']) . $tmpBrokenEnd . "</td>";
                    $text .= "<td align=\"left\" width=\"25%\">" . $tmpBrokenStart . $this->removeTagsFromText(IPS_GetObject($value3)['ObjectInfo']) . $tmpBrokenEnd . "</td></tr>" . PHP_EOL;
                }
                $text .= "</table>";
            }
        

            // Übersicht der Variablen
            // -----------------------
            if (($typeName == $this->Translate("VARIABLE")) && ($this->ReadPropertyBoolean("overviewVars"))) {
                $this->SendDebug(__FUNCTION__, "Starte Tag '" . $tagName . "' (VARIABLE)", 0);
                
                $text .= "<table border=\"1\" cellpadding=\"2px\">" . PHP_EOL;
                $text .= "<thead><tr style=\"font-weight:bold;background-color:#0b2f51;color:#ffffff;\">" . PHP_EOL;
                $text .= "<td align=\"center\" width=\"10%\">Id</td>" . PHP_EOL;
                $text .= "<td align=\"center\" width=\"45%\">" . $this->Translate("name and location") . "</td>" . PHP_EOL;
                $text .= "<td align=\"center\" width=\"10%\">" . $this->Translate("archived") . "</td>" . PHP_EOL;
                $text .= "<td align=\"center\" width=\"35%\">" . $this->Translate("description") . "</td>" . PHP_EOL;
                $text .= "</tr></thead>" . PHP_EOL;
                
                foreach ($this->tagList[$tagName][$typeName] as $key3 => $value3) {
                    $text .= "<tr><td align=\"center\" width=\"10%\">" . $value3 . "</td>";
                    $text .= "<td width=\"45%\">" . IPS_GetLocation($value3) . "</td>";

                    $text .= "<td align=\"center\" width=\"10%\">";
                    if (AC_GetLoggingStatus($this->ipsArchiveControlId, $value3) == 1) {
                        if (AC_GetAggregationType($this->ipsArchiveControlId, $value3) == 0) {
                            $text .= $this->Translate("yes") . " (" . $this->Translate("standard") . ")";
                        } else {
                            $text .= $this->Translate("yes") . " (" . $this->Translate("counter") . ")";
                        }
                    }
                    $text .= "</td>";

                    $text .= "<td width=\"35%\">" . $this->removeTagsFromText(IPS_GetObject($value3)['ObjectInfo']) . "</td></tr>" . PHP_EOL;
                }
                $text .= "</table>";
            }
            
            // Übersicht der Links
            // -------------------
            if (($typeName == $this->Translate("LINK")) && ($this->ReadPropertyBoolean("overviewLinks"))) {
                $this->SendDebug(__FUNCTION__, "Starte Tag '" . $tagName . "' (LINK)", 0);

                $text .= "<table border=\"1\" cellpadding=\"2px\">" . PHP_EOL;
                $text .= "<thead><tr align=\"center\" style=\"font-weight:bold;background-color:#0b2f51;color:#ffffff;\">" . PHP_EOL;
                $text .= "<td width=\"10%\">Id</td>" . PHP_EOL;
                $text .= "<td width=\"30%\">" . $this->Translate("name and location") . "</td>" . PHP_EOL;
                $text .= "<td width=\"30%\">" . $this->Translate("linked object") . "</td>" . PHP_EOL;
                $text .= "<td width=\"30%\">" . $this->Translate("description") . "</td>" . PHP_EOL;
                $text .= "</tr></thead>" . PHP_EOL;

                foreach ($this->tagList[$tagName][$typeName] as $key3 => $value3) {
                    $text .= "<tr align=\"center\"><td width=\"10%\">" . $value3 . "</td>";
                    $text .= "<td align=\"left\" width=\"30%\">" . IPS_GetLocation($value3) . "</td>";
                    $targetId = IPS_GetLink($value3)['TargetID'];
                    if (IPS_ObjectExists($targetId)) {
                        $text .= "<td width=\"30%\">" . IPS_GetLocation($targetId) . "</td>";
                    }
                    $text .= "<td width=\"30%\">" . $this->removeTagsFromText(IPS_GetObject($value3)['ObjectInfo']) . "</td></tr>" . PHP_EOL;
                }
                $text .= "</table>";
            }


            // Übersicht der Ereignisse
            // ------------------------
            if (($typeName == $this->Translate("EVENT")) && ($this->ReadPropertyBoolean("overviewEvent"))) {
                $this->SendDebug(__FUNCTION__, "Starte Tag '" . $tagName . "' (EVENT)", 0);
               
                $text .= "<table border=\"1\" cellpadding=\"2px\">" . PHP_EOL;
                $text .= "<thead><tr align=\"center\" style=\"font-weight:bold;background-color:#0b2f51;color:#ffffff;\">" . PHP_EOL;
                $text .= "<td width=\"10%\">Id</td>" . PHP_EOL;
                $text .= "<td width=\"30%\">" . $this->Translate("name and location") . "</td>" . PHP_EOL;
                $text .= "<td width=\"5%\">" . $this->Translate("number of childs") . "</td>" . PHP_EOL;
                $text .= "<td width=\"10%\">" . $this->Translate("event has conditions") . "</td>" . PHP_EOL;
                $text .= "<td width=\"20%\">" . $this->Translate("event type") . "</td>" . PHP_EOL;
                $text .= "<td width=\"25%\">" . $this->Translate("description") . "</td>" . PHP_EOL;
                $text .= "</tr></thead>" . PHP_EOL;
                
                foreach ($this->tagList[$tagName][$typeName] as $key3 => $value3) {
                    $text .= "<tr align=\"center\"><td width=\"10%\">" . $value3 . "</td>";
                    $text .= "<td align=\"left\" width=\"30%\">" . IPS_GetLocation($value3) . "</td>";
                    $text .= "<td width=\"5%\">" . count(IPS_GetChildrenIDs($value3)) . "</td>";

                    $text .= "<td width=\"10%\">";
                    if (count(IPS_GetEvent($value3)['EventConditions'])>0) {
                        $text .= $this->Translate("yes");
                    }
                    $text .= "</td>" . PHP_EOL;
                    
                    $text .= "<td width=\"20%\">" . $this->ipsEventType[IPS_GetEvent($value3)['EventType']] . "</td>";
                    $text .= "<td width=\"25%\">" . $this->removeTagsFromText(IPS_GetObject($value3)['ObjectInfo']) . "</td></tr>" . PHP_EOL;
                }
                $text .= "</table>" . PHP_EOL;
            }

            // Übersicht der Instanzen
            // -----------------------
            if (($typeName == $this->Translate("INSTANCE")) && ($this->ReadPropertyBoolean("overviewInstances"))) {
                $this->SendDebug(__FUNCTION__, "Starte Tag '" . $tagName . "' (INSTANCE)", 0);
                
                $text .= "<table border=\"1\" cellpadding=\"2px\">" . PHP_EOL;
                $text .= "<thead><tr align=\"center\" style=\"font-weight:bold;background-color:#0b2f51;color:#ffffff;\">" . PHP_EOL;
                $text .= "<td width=\"10%\">Id</td>" . PHP_EOL;
                $text .= "<td width=\"35%\">" . $this->Translate("name and location") . "</td>" . PHP_EOL;
                $text .= "<td width=\"20%\">" . $this->Translate("instance status") . "</td>" . PHP_EOL;
                $text .= "<td width=\"35%\">" . $this->Translate("description") . "</td>" . PHP_EOL;
                $text .= "</tr></thead>" . PHP_EOL;

                foreach ($this->tagList[$tagName][$typeName] as $key3 => $value3) {
                    $text .= "<tr align=\"center\"><td width=\"10%\">" . $value3 . "</td>";
                    $text .= "<td align=\"left\" width=\"35%\">" . IPS_GetLocation($value3) . "</td>";

                    if (IPS_GetInstance($value3)['InstanceStatus'] < 200) {
                        $text .= "<td width=\"20%\">" . $this->ipsInstanceStatus[IPS_GetInstance($value3)['InstanceStatus']] . "</td>" . PHP_EOL;
                    } else {
                        $text .= "<td width=\"20%\">" . $this->Translate("instance broken (unknown)") . "</td>" . PHP_EOL;
                    }
                    
                    $text .= "<td width=\"35%\">" . $this->removeTagsFromText(IPS_GetObject($value3)['ObjectInfo']) . "</td></tr>" . PHP_EOL;
                }
                $text .= "</table>";
            }

            // Übersicht der Medien
            // --------------------
            if (($typeName == $this->Translate("MEDIA")) && ($this->ReadPropertyBoolean("overviewMedia"))) {
                $this->SendDebug(__FUNCTION__, "Starte Tag '" . $tagName . "' (MEDIA)", 0);
                
                $text .= "<table border=\"1\" cellpadding=\"2px\">" . PHP_EOL;
                $text .= "<thead>" . PHP_EOL;
                $text .= "<tr align=\"center\" style=\"font-weight:bold;background-color:#0b2f51;color:#ffffff\">" . PHP_EOL;
                $text .= "<td width=\"10%\">Id</td>" . PHP_EOL;
                $text .= "<td align=\"left\" width=\"25%\">" . $this->Translate("name and location") . "</td>" . PHP_EOL;
                $text .= "<td width=\"15%\">" . $this->Translate("media file") . "</td>" . PHP_EOL;
                $text .= "<td width=\"15%\">" . $this->Translate("media type") . "</td>" . PHP_EOL;
                $text .= "<td width=\"15%\">" . $this->Translate("media size") . "</td>" . PHP_EOL;
                $text .= "<td width=\"20%\">" . $this->Translate("description") . "</td>" . PHP_EOL;
                $text .= "</tr></thead>" . PHP_EOL;
                
                foreach ($this->tagList[$tagName][$typeName] as $key3 => $value3) {
                    $text .= "<tr align=\"center\"><td width=\"10%\">" . $value3 . "</td>" . PHP_EOL;
                    $text .= "<td align=\"left\" width=\"25%\">" . IPS_GetLocation($value3) . "</td>" . PHP_EOL;
                    $text .= "<td width=\"15%\">" . IPS_GetMedia($value3)['MediaFile'] . "</td>" . PHP_EOL;
                    $text .= "<td width=\"15%\">" . $this->ipsMediaType[IPS_GetMedia($value3)['MediaType']] . "</td>" . PHP_EOL;
                    $text .= "<td width=\"15%\">" . round(IPS_GetMedia($value3)['MediaSize'] / 1024) . " KB </td>" . PHP_EOL;
                    $text .= "<td width=\"20%\">" . $this->removeTagsFromText(IPS_GetObject($value3)['ObjectInfo']) . "</td></tr>" . PHP_EOL;
                }

                $text .= "</table>";
            }



            $timeStop = microtime(true);
            $dauer = round(($timeStop - $timeStart), 2);
            $this->SendDebug(__FUNCTION__, "Overview Erzeugung nach Tags nach " . $dauer . " Sekunden abgeschlossen", 0);

            return $text;
        }


        /**
         * genOverviewArray
         *
         * @return void
         */
        private function genOverviewArray()
        {
            $this->SendDebug(__FUNCTION__, "Durchsuche alle ObjectInfos auf Tags", 0);
            $timeStart = microtime(true);

            foreach (IPS_GetObjectList() as $o) {
                $tmp1 = IPS_GetObject($o);

                if ($tmp1['ObjectType'] > 0) {
                    // Ignore categories
                    $desc = $tmp1['ObjectInfo'];
                    $type = $this->ipsObjectType[$tmp1['ObjectType']];
                    foreach ($this->getTagFromText($desc) as $tag) {
                        $this->tagList[$tag][strtoupper($type)][] = $o;
                    }
                }
            }

            $timeStop = microtime(true);
            $dauer = round(($timeStop - $timeStart), 2);
            $this->SendDebug(__FUNCTION__, "Das Zusammenstellen aller Tags hat " . $dauer . " Sekunden gedauert.", 0);
        }



        /**
         * getTagFromText
         *
         * @param  mixed $text
         *
         * @return void
         */
        private function getTagFromText($text)
        {
            $erg = array();

            if (strlen($text) > 0) {
                $words = explode(" ", $text);

                foreach ($words as $word) {
                    $word = trim($word);
                    if (strlen($word)>0) {
                        if ($word[0] == "#") {
                            $tmp = strtoupper(str_replace("#", "", $word));
                            array_push($erg, $tmp);
                        }
                    }
                }
            }
            
            if (count($erg)== 0) {
                // Text is empty
                array_push($erg, "UNTAGGED");
            }
        
            return $erg;
        }


        /**
         * Remove found tags from object info if option is set in configururation
         *
         * @param  mixed $text
         *
         * @return void
         */
        private function removeTagsFromText($text)
        {
            //$this->SendDebug(__FUNCTION__, "Entferne Tags aus Text '" . $text . "'", 0);
            if ($this->ReadPropertyBoolean("overviewRemoveDescTags")) {
                return preg_replace("/#(\w+)/", "", $text);
            } else {
                return $text;
            }
        }

        /**
          * Creates pdf pages for all SCRIPT OBJECTS
          *
          * @return void
        */
        private function createScriptFiles()
        {
            $this->SendDebug(__FUNCTION__, "Starte mit der Erzeugung der Skript Detail Seiten", 0);
            $timeStart = microtime(true);

            $this->pdf->Bookmark('Scripts', 1, -1, '', '', array(0,0,0));
            $scriptList = IPS_GetScriptList();

            $this->SendDebug(__FUNCTION__, count($scriptList) . " Skripte werden dokumentiert", 0);
            foreach ($scriptList as $value) {
                $text = "<h2>Script #" . $value . " - " . IPS_GetName($value) . "</h2>";

                $this->pdf->Bookmark($value . "(" . IPS_GetName($value) . ")", 2, -1, '', '', array(0,0,0));
                $text .= "<a name=\"". $value . "\"></a>";

                $text .= $this->getObjectHeader($value);

                $text .= "<br><br>";
                $this->pdf->writeHTML($text, true, false, true, false, '');
            }

            $timeStop = microtime(true);
            $dauer = round(($timeStop - $timeStart), 2);
            $this->SendDebug(__FUNCTION__, "Erstellung der Skript Detailseiten in " . $dauer . " Sekunden abgeschlossen", 0);
        }

        private function createVariableFiles()
        {
            $this->SendDebug(__FUNCTION__, "Starte mit der Erzeugung der Variablen Detail Seiten", 0);
            $timeStart = microtime(true);

            $varList = IPS_GetVariableList();
            $this->SendDebug(__FUNCTION__, count($varList) . " Variablen werden dokumentiert", 0);
            foreach ($varList as $value) {
                $text = "<h2>Variable #" . $value . " - " . IPS_GetName($value) . "</h2>";

                $this->pdf->Bookmark($value . "(" . IPS_GetName($value) . ")", 2, -1, '', '', array(0,0,0));
                $text .= "<a name=\"". $value . "\"></a>";

                $text .= $this->getObjectHeader($value);

                $text .= "<ul>" . PHP_EOL;
                $text .= "<li>" . $this->Translate("custom profile") . ": " . IPS_GetVariable($value)['VariableCustomProfile'] . PHP_EOL;
                $text .= "<li>" . $this->Translate("profile") . ": " . IPS_GetVariable($value)['VariableProfile'] . PHP_EOL;
                $text .= "<li>" . $this->Translate("variable type") . ": " . $this->ipsVariableType[IPS_GetVariable($value)['VariableType']] . PHP_EOL;
                $text .= "<li>" . $this->Translate("custom action script") . ": " . IPS_GetVariable($value)['VariableCustomAction'] . PHP_EOL;

                $text .= "</ul><br><br>";
                $this->pdf->writeHTML($text, true, false, true, false, '');
            }

            $timeStop = microtime(true);
            $dauer = round(($timeStop - $timeStart), 2);
            $this->SendDebug(__FUNCTION__, "Erstellung der Variablen Detailseiten in " . $dauer . " Sekunden abgeschlossen", 0);
        }


        /**
         * Generated html text with common object information using IPS_GetObject
         *
         * @param  int $id object id
         *
         * @return string md syntax with common object information
         */
        private function getObjectHeader($id)
        {
            $erg = IPS_GetObject($id);
            
            $text = IPS_GetLocation($id) . "<br><br>" . PHP_EOL;

            $text = "<u><b>" . $this->Translate("Common object information") . "</u></b>". PHP_EOL;
            $text .= "<ul>";
            $text .= "<li> " . $this->Translate("object icon") . ": " . $erg['ObjectIcon'] . "</li>" . PHP_EOL;
            $text .= "<li> " . $this->Translate("object ident") . ": " . $erg['ObjectIdent'] . "</li>" . PHP_EOL;
            $text .= "<li> " . $this->Translate("object info") . ": " . $erg['ObjectInfo'] . "</li>" . PHP_EOL;
            
            if ($erg['ObjectIsDisabled'] == 1) {
                $text .= "<li> " . $this->Translate("is object disabled?") . ": " . $this->Translate("yes") . "</li>" . PHP_EOL;
            } else {
                $text .= "<li> " . $this->Translate("is object disabled?") . ": " . $this->Translate("no") . "</li>" . PHP_EOL;
            }
            $text .= "</ul><br>" . PHP_EOL;


            $childIds = IPS_GetChildrenIDs($id);
            if (count($childIds) > 0) {
                $text .= "<u><b> " . $this->Translate("child elements") . "</u></b><br><br>" . PHP_EOL;

                $text .= "<table border=\"1\" cellpadding=\"2px\">" . PHP_EOL;
                $text .= "<thead>" . PHP_EOL;
                $text .= "<tr align=\"center\" style=\"font-weight:bold;background-color:#0b2f51;color:#ffffff\">" . PHP_EOL;
                $text .= "<td width=\"10%\">Id</td>" . PHP_EOL;
                $text .= "<td align=\"left\" width=\"20%\">" . $this->Translate("object Type") . "</td>" . PHP_EOL;
                $text .= "<td width=\"35%\">" . $this->Translate("object name") . "</td>" . PHP_EOL;
                $text .= "<td width=\"35%\">" . $this->Translate("object description") . "</td>" . PHP_EOL;
                $text .= "</tr></thead>" . PHP_EOL;
                
                foreach ($childIds as $key => $val) {
                    $text .= "<tr align=\"center\"><td width=\"10%\">" . $val . "</td>" . PHP_EOL;
                    $text .= "<td align=\"left\" width=\"20%\">" . $this->ipsObjectType[IPS_GetObject($val)['ObjectType']] . "</td>" . PHP_EOL;
                    $text .= "<td width=\"35%\">" . IPS_GetName($val) . "</td>" . PHP_EOL;
                    $text .= "<td width=\"35%\">" . IPS_GetObject($val)['ObjectInfo'] . "</td></tr>" . PHP_EOL;
                }
                $text .= "</table>";
            }

            return $text;
        }
    }
