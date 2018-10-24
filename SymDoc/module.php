<?php

/**
  * The module "SymDoc" generated an md-based text documentation of an IP-Symcon installation.
  *
  * @author Thorsten Mueller <MrThorstenM (at) gmx.net> / thorsten9
  * @since 5.0.0
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

        private $outputFolderOverview;
        private $outputFolderDetails;
        private $tagList = array();

        // -------------------------------------------------------------------------------

        // Der Konstruktor des Moduls
        // Überschreibt den Standard Kontruktor von IPS
        public function __construct($InstanceID)
        {
            parent::__construct($InstanceID);
        }
 
        // Überschreibt die interne IPS_Create($id) Funktion
        public function Create()
        {
            parent::Create();

            $this->RegisterPropertyString("outputFolder", "");
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
            $this->RegisterPropertyBoolean("scriptInclude", true);
            $this->RegisterPropertyBoolean("overviewRemoveDescTags", false);
            
            $idLastExec = $this->RegisterVariableString("SymDoc_LastExec", $this->Translate("last doc generation"), "~String", 0);
            IPS_SetInfo($idLastExec, "Zeitpunkt wann die letzte Doku mit #SymDoc erzeugt wurde.");
            $idPrefixVar = $this->RegisterVariableString("SymDoc_PrefixText", $this->Translate("individual prefix text"), "~TextBox", 0);
            IPS_SetInfo($idLastExec, "Variable für individuellen Text im Kopf der erzeugten #SymDoc Doku.");
            $idActionScript = IPS_CreateScript(0);
            IPS_SetParent($idActionScript, $idPrefixVar);
            IPS_SetName($idActionScript, $this->Translate("SymDoc Action Script"));
            $scriptContent = "<? " . PHP_EOL;
            $scriptContent .= 'SetValue($_IPS["VARIABLE"], $_IPS["VALUE"]);' . PHP_EOL;
            $scriptContent .= "?>";

            IPS_SetScriptContent($idActionScript, $scriptContent);
            IPS_SetVariableCustomAction($idPrefixVar, $idActionScript);
            IPS_SetHidden($idActionScript, true);
        }
 
        public function ApplyChanges()
        {
            if (strlen($this->ReadPropertyString("outputFolder"))>0) {
                if (!is_dir($this->ReadPropertyString("outputFolder"))) {
                    die($this->Translate("The configured path is not a directory"));
                }
            }
            
            parent::ApplyChanges();
        }

        // -------------------------------------------------------------------------------



        /**
         * Set required constants including the ips required translation.
         *
         * @return void
         */
        private function setConsts()
        {
            $this->ipsUtilControlId = IPS_GetInstanceListByModuleID("{B69010EA-96D5-46DF-B885-24821B8C8DBD}")[0];
            $this->ipsArchiveControlId = IPS_GetInstanceListByModuleID("{43192F0B-135B-4CE7-A0A7-1475603F3060}")[0];

            // Common constants
            $dateTime = date("Y-m-d");
            $this->outputFolderOverview = $this->ReadPropertyString("outputFolder") . "/" . $dateTime;
            $this->outputFolderDetails = $this->outputFolderOverview . "/" . "details";

            // Get IPS snapshot
            $tmp = utf8_encode(IPS_GetSnapshot());
            $this->ipsSnapshot = json_decode($tmp, true);

            // Set object types
            array_push($this->ipsObjectType, $this->Translate('category'));
            array_push($this->ipsObjectType, $this->Translate('instance'));
            array_push($this->ipsObjectType, $this->Translate('variable'));
            array_push($this->ipsObjectType, $this->Translate('script'));
            array_push($this->ipsObjectType, $this->Translate('event'));
            array_push($this->ipsObjectType, $this->Translate('media'));
            array_push($this->ipsObjectType, $this->Translate('link'));

            // Set event types
            array_push($this->ipsEventCyclingTime, $this->Translate('daily'));
            array_push($this->ipsEventCyclingTime, $this->Translate('once'));
            array_push($this->ipsEventCyclingTime, $this->Translate('daily'));
            array_push($this->ipsEventCyclingTime, $this->Translate('weekly'));
            array_push($this->ipsEventCyclingTime, $this->Translate('monthly'));
            array_push($this->ipsEventCyclingTime, $this->Translate('daily'));
        
            // Set event trigger
            array_push($this->ipsEventType, $this->Translate('triggered'));
            array_push($this->ipsEventType, $this->Translate('cyclic'));
            array_push($this->ipsEventType, $this->Translate('weekplan'));

            // Set event trigger type details
            array_push($this->ipsEventTriggerType, $this->Translate('on variable update'));
            array_push($this->ipsEventTriggerType, $this->Translate('on variable change'));
            array_push($this->ipsEventTriggerType, $this->Translate('on limit drop'));
            array_push($this->ipsEventTriggerType, $this->Translate('on limit exceed'));
            array_push($this->ipsEventTriggerType, $this->Translate('on defined value'));

            // Set event cyclic details
            array_push($this->ipsEventCyclicDate, $this->Translate('no date type'));
            array_push($this->ipsEventCyclicDate, $this->Translate('once'));
            array_push($this->ipsEventCyclicDate, $this->Translate('daily'));
            array_push($this->ipsEventCyclicDate, $this->Translate('weekly'));
            array_push($this->ipsEventCyclicDate, $this->Translate('monthly'));
            array_push($this->ipsEventCyclicDate, $this->Translate('yearly'));

            // Set variable types
            array_push($this->ipsVariableType, "Boolean");
            array_push($this->ipsVariableType, "Integer");
            array_push($this->ipsVariableType, "Float");
            array_push($this->ipsVariableType, "String");

            // Set event conditions on vars
            array_push($this->ipsEventConditionVar, $this->Translate("equals"));
            array_push($this->ipsEventConditionVar, $this->Translate("unequal"));
            array_push($this->ipsEventConditionVar, $this->Translate("greater"));
            array_push($this->ipsEventConditionVar, $this->Translate("greater or equal"));
            array_push($this->ipsEventConditionVar, $this->Translate("less"));
            array_push($this->ipsEventConditionVar, $this->Translate("less or equal"));

            array_push($this->ipsEventConditionType, $this->Translate("all conditions must apply"));
            array_push($this->ipsEventConditionType, $this->Translate("just one condition must apply"));

            $this->ipsInstanceStatus[101] = $this->Translate("Instance will be created");
            $this->ipsInstanceStatus[102] = $this->Translate("Instance is active");
            $this->ipsInstanceStatus[103] = $this->Translate("Instance will be deleted");
            $this->ipsInstanceStatus[104] = $this->Translate("Instance is inactiv");
            $this->ipsInstanceStatus[200] = $this->Translate("ups");
            $this->ipsInstanceStatus[201] = $this->Translate("ups");
        

            array_push($this->ipsMediaType, $this->Translate("form"));
            array_push($this->ipsMediaType, $this->Translate("image"));
            array_push($this->ipsMediaType, $this->Translate("sound"));
            array_push($this->ipsMediaType, $this->Translate("stream"));
            array_push($this->ipsMediaType, $this->Translate("chart"));
        }

        // ==============================================================
        // CREATE ONE DETAIL PAGE PER TYPE (SCRIPTS,VARS,INSTANCES,MEDIA)
        // ==============================================================
        
        /**
          * Creates .md files for all SCRIPT OBJECTS in the "details" directory
          *
          * @return void
          */
        private function createScriptFiles()
        {
            $scriptList = IPS_GetScriptList();
            foreach ($scriptList as $value) {
                $text = $this->getObjectHeader($value);

                $text .= "### " . $this->Translate("script information") . PHP_EOL;
                $text .= "* " . $this->Translate("script file") . ": " . IPS_GetScript($value)['ScriptFile'] . PHP_EOL . PHP_EOL;
                
                if ($this->ReadPropertyBoolean("scriptInclude")) {
                    $text .= "### " . $this->Translate("script content") . PHP_EOL;
                    $text .= "```php" . PHP_EOL . IPS_GetScriptContent($value) . PHP_EOL . "```" . PHP_EOL;
                }

                file_put_contents($this->outputFolderDetails . "/" . $value . ".md", utf8_decode($text) . PHP_EOL);
            }
        }



        /**
         * Creates .md files for all VARIABLE OBJECTS in the "details" directory
         *
         * @return void
         */
        private function createVariableFiles()
        {
            $varList = IPS_GetVariableList();
            foreach ($varList as $value) {
                $text = $this->getObjectHeader($value);

                $text .= "### " . $this->Translate("variable information") . PHP_EOL;
                $text .= "* " . $this->Translate("custom profile") . ": " . IPS_GetVariable($value)['VariableCustomProfile'] . PHP_EOL;
                $text .= "* " . $this->Translate("profile") . ": " . IPS_GetVariable($value)['VariableProfile'] . PHP_EOL;
                $text .= "* " . $this->Translate("variable type") . ": " . $this->ipsVariableType[IPS_GetVariable($value)['VariableType']] . PHP_EOL;
                $text .= "* " . $this->Translate("custom action script") . ": " . IPS_GetVariable($value)['VariableCustomAction'] . PHP_EOL;

                file_put_contents($this->outputFolderDetails . "/" . $value . ".md", utf8_decode($text) . PHP_EOL);
            }
        }

   
        /**
         * Creates .md files for all EVENT OBJECTS in the "details" directory
         *
         * @return void
         * @todo beautify cyclic and weekplan event details
         */
        private function createEventFiles()
        {
            $varList = IPS_GetEventList();
            foreach ($varList as $value) {
                $text = $this->getObjectHeader($value);
                $event = IPS_GetEvent($value);

                // get event conditions
                if (count($event['EventConditions'])>0) {
                    $text .= "## " . $this->Translate("event conditions") . PHP_EOL;
                    
                    $text .= "> " . $this->ipsEventConditionType[$event['EventConditions'][0]['Operation']] . PHP_EOL;

                    // get event conditions (variable rules)
                    $erg = $event['EventConditions'][0]['VariableRules'];
                    if (count($erg) > 0) {
                        $text .= "### " . $this->Translate("variable conditions") . PHP_EOL;

                        $text .= "| " . $this->Translate("variable");
                        $text .= "| " . $this->Translate("variable location");
                        $text .= "| " . $this->Translate("comparison");
                        $text .= "| " . $this->Translate("value") . PHP_EOL;
            
                        $text .= "| --- | --- | --- | --- |" . PHP_EOL;
            
                        foreach ($erg as $key => $val) {
                            $text .= "| [" . $val['VariableID'] . "](./" . $val['VariableID'] . ".md)";
                            $text .= "| " . IPS_GetLocation($val['VariableID']);
                            $text .= "| " . $this->ipsEventConditionVar[$val['Comparison']];
                            $text .= "| " . $val['Value'] . PHP_EOL;
                        }
                    }
           
                    // get event conditions (date rules)
                    $erg = $event['EventConditions'][0]['DateRules'];
                    if (count($erg) > 0) {
                        $text .= "### " . $this->Translate("date conditions") . PHP_EOL;
                   
                        $text .= "| " . $this->Translate("date");
                        $text .= "| " . $this->Translate("comparison") . PHP_EOL;
                 
                               
                        $text .= "| --- | --- |" . PHP_EOL;
                               
                        foreach ($erg as $key => $val) {
                            $text .= "| " . $val['Value']['Day'] . "." . $val['Value']['Month'] . "." . $val['Value']['Year'];
                            $text .= "| " . $this->ipsEventConditionVar[$val['Comparison']] . PHP_EOL;
                            ;
                        }
                    }

                    // get event conditions (time rules)
                    $erg = $event['EventConditions'][0]['TimeRules'];
                    if (count($erg) > 0) {
                        $text .= "### " . $this->Translate("time conditions") . PHP_EOL;

                        $text .= "| " . $this->Translate("time");
                        $text .= "| " . $this->Translate("comparison") . PHP_EOL;

            
                        $text .= "| --- | --- |" . PHP_EOL;
            
                        foreach ($erg as $key => $val) {
                            $text .= "| " . $val['Value']['Hour'] . ":" . $val['Value']['Minute'] . ":" . $val['Value']['Second'];
                            $text .= "| " . $this->ipsEventConditionVar[$val['Comparison']] . PHP_EOL;
                            ;
                        }
                    }
                }

                $text .= "## " . $this->Translate("event information") . PHP_EOL;
                
                $text .= "* " . $this->Translate("event type: ");
                $text .= $this->ipsEventType[$event['EventType']] . PHP_EOL;

                $text .= "* " . $this->Translate("event active: ");
                if ($event['EventActive'] == 1) {
                    $text .= $this->Translate("yes") . PHP_EOL;
                } else {
                    $text .= $this->Translate("no") . PHP_EOL;
                }


                if ($event['EventType'] == 0) {
                    // triggered event
                    $text .= "### " . $this->Translate("triggered event details") . PHP_EOL;
                    
                    $text .= "* " . $this->ipsEventTriggerType[$event['TriggerType']] . PHP_EOL;
                    $text .= "* " . $this->Translate("affected var") . ": ";
                    $text .= "[" . $event['TriggerVariableID'] . "](./" . $event['TriggerVariableID'] . ".md) ";

                    if (IPS_VariableExists($event['TriggerVariableID'])) {
                        $text .= " (" . IPS_GetLocation($event['TriggerVariableID']) . ")" . PHP_EOL;
                    } else {
                        $text .= " (" . $this->Translate("affected variable id does not exist") . ")" . PHP_EOL;
                    }
                }

                if ($event['EventType'] == 1) {
                    // cyclic event
                    $text .= "### " . $this->Translate("cyclic event details") . PHP_EOL;

                    $text .= "* " . $this->Translate("cycling date type: ");
                    $text .= "every " . $event['CyclicDateValue'] . $this->ipsEventCyclicDate[$event['CyclicDateType']] . " " . PHP_EOL;
                   
                    if ($event['CyclicDateDay'] > 0) {
                        // days of week has been selected
                        $tmp = array(1, 2, 4, 8, 16, 32, 64);
                        $a = $event['CyclicDateDay'];

                        if ($event['CyclicDateDayValue'] > 0) {
                            $text .= " on " . $event['CyclicDateDayValue'] . PHP_EOL;
                        }

                        foreach ($tmp as $t) {
                            $result = $t & $a;

                            switch ($result) {
                                case 1: $text .= "  * " . $this->Translate("monday") . PHP_EOL;break;
                                case 2: $text .= "  * " . $this->Translate("tuesday") . PHP_EOL;break;
                                case 4: $text .= "  * " . $this->Translate("wednesday") . PHP_EOL;break;
                                case 8: $text .= "  * " . $this->Translate("thursday") . PHP_EOL;break;
                                case 16: $text .= "  * " . $this->Translate("friday") . PHP_EOL;break;
                                case 32: $text .= "  * " . $this->Translate("saturday") . PHP_EOL;break;
                                case 64: $text .= "  * " . $this->Translate("sunday") . PHP_EOL;break;
                            }
                        }
                    }
                    
                    $text .= "* " . $this->Translate("date span: ");
                    $text .= $event['CyclicDateFrom']['Day'] . ".";
                    $text .= $event['CyclicDateFrom']['Month'] . ".";
                    $text .= $event['CyclicDateFrom']['Year'] . " - ";
                    $text .= $event['CyclicDateTo']['Day'] . ".";
                    $text .= $event['CyclicDateTo']['Month'] . ".";
                    $text .= $event['CyclicDateTo']['Year'] . PHP_EOL;

                    $text .= "* " . $this->Translate("time span: ");
                    $text .= $event['CyclicTimeFrom']['Hour'] . ":";
                    $text .= $event['CyclicTimeFrom']['Minute'] . ":";
                    $text .= $event['CyclicTimeFrom']['Second'] . " - ";
                    $text .= $event['CyclicTimeTo']['Hour'] . ":";
                    $text .= $event['CyclicTimeTo']['Minute'] . ":";
                    $text .= $event['CyclicTimeTo']['Second'] . PHP_EOL;
                }

                if ($event['EventType'] == 2) {
                    // weekplan event
                    $text .= "### " . $this->Translate("weekplan event details") . PHP_EOL;

                    $text .= "* " . $this->Translate("the weekplan has the following options: ") . PHP_EOL;
                    foreach ($event['ScheduleActions'] as $weekplanActionKey => $weekplanActionValue) {
                        $text .= "  * " . $weekplanActionValue['Name'] . PHP_EOL;
                    }
                }

                file_put_contents($this->outputFolderDetails . "/" . $value . ".md", utf8_decode($text) . PHP_EOL);
            }
        }

        /**
         * Creates .md files for all INSTANCE OBJECTS in the "details" directory
         *
         * @return void
         */
        private function createInstanceFiles()
        {
            $instList = IPS_GetInstanceList();
            foreach ($instList as $value) {
                $text = $this->getObjectHeader($value);

                $text .= "### " . $this->Translate("instance information") . PHP_EOL;

                $inst = IPS_GetInstance($value);


                $text .= "* " . $this->Translate("instance status") . ": " . $this->ipsInstanceStatus[$inst['InstanceStatus']] . PHP_EOL;
                
                if (is_array($inst['ModuleInfo'])) {
                    $text .= "* " . $this->Translate("module name") . ": " . $inst['ModuleInfo']['ModuleName'] . PHP_EOL;
           
                    /*
                    $modDetails = IPS_GetModule($inst['ModuleInfo']['ModuleID']);

                    $text .= "* " . $modDetails['Vendor'] . PHP_EOL;
                    foreach ($modDetails['Aliases'] as $alias) {
                        $text .= "Alias: " . $alias . PHP_EOL;
                    }
                    */
                }

                file_put_contents($this->outputFolderDetails . "/" . $value . ".md", utf8_decode($text) . PHP_EOL);
            }
        }

        /**
         * Creates .md files for all MEDIA OBJECTS in the "details" directory
         *
         * @return void
         */
        private function createMediaFiles()
        {
            $mediaList = IPS_GetMediaList();
            foreach ($mediaList as $value) {
                $text = $this->getObjectHeader($value);

                $text .= "### " . $this->Translate("media information") . PHP_EOL;

                $media = IPS_GetMedia($value);

                $text .= "* " . $this->Translate("media file") . ": " . $media['MediaFile'] . PHP_EOL;
                $text .= "* " . $this->Translate("media is available") . ": " . $media['MediaIsAvailable'] . PHP_EOL;
                $text .= "* " . $this->Translate("media is cached") . ": " . $media['MediaIsCached'] . PHP_EOL;
                $text .= "* " . $this->Translate("media size in bytes") . ": " . $media['MediaSize'] . PHP_EOL;
                
                file_put_contents($this->outputFolderDetails . "/" . $value . ".md", utf8_decode($text) . PHP_EOL);
            }
        }

        // =========================
        // END DETAIL PAGES CREATION
        // =========================

        // ==================================
        // PREPARE SECTIONS FOR OVERVIEW PAGE
        // ==================================

        /**
          * Generates a text for the top of the overview page.
          * This includes:
          * - Title (Name of root id 0)
          * - Date and time of document creation
          * - Machine name where document was created
          * - Individual content based on instance variable
          *
          * @return String the md formatted header text
          */
        private function overviewHeader()
        {
            $text = "# " . $this->Translate("Symcon documentation:" . " " . IPS_GetName(0)) . PHP_EOL;
            $text .= "> " . sprintf($this->Translate("this documentation was created automatically on %s for machine %s"), date("d.m.Y H:i"), gethostname());
            $text .= PHP_EOL . PHP_EOL;

            if ($this->ReadPropertyBoolean("overviewPrefixText")) {
                // Individual text info should be added
                $text .= GetValue($this->GetIDForIdent("SymDoc_PrefixText")) . PHP_EOL . PHP_EOL;
            }
   
            return $text;
        }

        /**
          * Generates a footer text for the overview page.
          * @return String the md formatted header text
          */
        private function overviewFooter()
        {
            $text = PHP_EOL . PHP_EOL . "---" . PHP_EOL;
            $text .=  "> " . $this->Translate("this module is provided by thorsten9. Use on your own risk. No kind of warrenties!");
            $text .= PHP_EOL;
  
            return $text;
        }
  

        /**
         * Generates a table text with extended IPS properties
         *
         * @return String the md formatted table text with IPS extended properties
         */
        private function overviewExtProps()
        {
            $erg = $this->ipsSnapshot['options'];
            
            $text = "## " . $this->Translate("Symcon extended properties") . PHP_EOL;
            $text .= "| " . $this->Translate("Key") . " | " . $this->Translate("Value") . " | " . PHP_EOL;
            $text .= "| --- | --- |" . PHP_EOL;
            
            foreach ($erg as $key => $value) {
                $text .= "| " . $key . " | " . $value . " | " . PHP_EOL;
            }

            return $text;
        }

        /**
         * Generates a table text with generic IPS information
         *
         * @return String the md formatted table text with generic IPS information
         */
        private function overviewGenericInfo()
        {
            $text = "## " . $this->Translate("Generic program information") . PHP_EOL;

            $text .= "| " . $this->Translate("Key") . " | " . $this->Translate("Value") . " | " . PHP_EOL;
            $text .= "| --- | --- |" . PHP_EOL;
            $text .= "| " . $this->Translate("program directory") . " | " . IPS_GetKernelDir() . " | " . PHP_EOL;
            $text .= "| " . $this->Translate("platform") . " | " . IPS_GetKernelPlatform() . " | " . PHP_EOL;
            $text .= "| " . $this->Translate("kernel version") . " | " . IPS_GetKernelVersion() . " | " . PHP_EOL;
            $text .= "| " . $this->Translate("kernel revision") . " | " . IPS_GetKernelRevision() . " | " . PHP_EOL;
            $text .= "| " . $this->Translate("log directory") . " | " . IPS_GetLogDir() . " | " . PHP_EOL;
            
            $subscriptionValidUntil = date("d.m.Y H:i", GetValue(IPS_GetObjectIDByIdent("LicenseSubscription", $this->ipsUtilControlId)));
            $text .= "| " . $this->Translate("subscription valid to") . " | " . $subscriptionValidUntil . " | " . PHP_EOL;

            return $text;
        }

        /**
         * Generate .md File (index.md) as overview grouped by tags within object description.
         *
         * @return String the md formatted string with table overviews of all tags and object types
         */
        private function genOverviewByTag()
        {
            $text = "";
            $tagArray = $this->tagList;
            foreach ($tagArray as $tagName => $value) {
                $text .= PHP_EOL . "---" . PHP_EOL;
                $text .= "## " . $tagName . PHP_EOL;
                $text .= "> [" . $this->Translate("back to toc") . "](./index.md#" . strtolower($this->Translate("toc")). ")" . PHP_EOL . PHP_EOL;
                
                $typeArray = $this->tagList[$tagName];
                foreach ($typeArray as $typeName => $value2) {
                    if (is_array($this->tagList[$tagName][$typeName])) {
                        if (($typeName == $this->Translate("SCRIPT")) && ($this->ReadPropertyBoolean("overviewScripts"))) {
                            $text .= "### " . $tagName . " (" . $typeName . ")" . PHP_EOL;
                            $text .= "| Id";
                            $text .= " | " . $this->Translate("name and location");
                            $text .= " | " . $this->Translate("number of childs");
                            $text .= " | " . $this->Translate("number of references");
                            $text .= " | " . $this->Translate("last execution");
                            $text .= " | " . $this->Translate("description") . PHP_EOL;
                            $text .= "| --- | --- | --- | --- | --- | --- |" . PHP_EOL;
    
                            foreach ($this->tagList[$tagName][$typeName] as $key3 => $value3) {
                                if ((IPS_GetScript($value3)['ScriptIsBroken']) && ($this->ReadPropertyBoolean("overviewStrikeBrokenScripts"))) {
                                    $tmpBrokenStart = "<del>";
                                    $tmpBrokenEnd = "</del>";
                                } else {
                                    $tmpBrokenStart = "";
                                    $tmpBrokenEnd = "";
                                }

                                $text .= "| [" . $tmpBrokenStart . $value3 . "](./details/" . $value3 . ".md)" . $tmpBrokenEnd;
                                $text .= "| " . $tmpBrokenStart . IPS_GetLocation($value3) . $tmpBrokenEnd;
                                $text .= "| " . $tmpBrokenStart . count(IPS_GetChildrenIDs($value3)) . $tmpBrokenEnd;
                                $text .= "| " . $tmpBrokenStart . count(UC_FindReferences($this->ipsUtilControlId, $value3)) . $tmpBrokenEnd;
                                $text .= "| " . $tmpBrokenStart . date("d.m.Y H:i", IPS_GetScript($value3)['ScriptExecuted']) . $tmpBrokenEnd;
                                $text .= "| " . $tmpBrokenStart . $this->removeTagsFromText(IPS_GetObject($value3)['ObjectInfo']) . $tmpBrokenEnd . PHP_EOL;
                            }
                        }

                        if (($typeName == $this->Translate("VARIABLE")) && ($this->ReadPropertyBoolean("overviewVars"))) {
                            $text .= "### " . $tagName . " (" . $typeName . ")" . PHP_EOL;
                            $text .= "| Id";
                            $text .= " | " . $this->Translate("name and location");
                            $text .= " | " . $this->Translate("number of childs");
                            $text .= " | " . $this->Translate("number of references");
                            $text .= " | " . $this->Translate("archived");
                            $text .= " | " . $this->Translate("description") . PHP_EOL;
                            $text .= "| --- | --- | --- | --- | --- | --- |" . PHP_EOL;
        
                            
                            foreach ($this->tagList[$tagName][$typeName] as $key3 => $value3) {
                                $text .= "| [" . $value3 . "](./details/" . $value3 . ".md)";
                                $text .= "| " . IPS_GetLocation($value3);
                                $text .= "| " . count(IPS_GetChildrenIDs($value3));
                                $text .= "| " . count(UC_FindReferences($this->ipsUtilControlId, $value3));
                                $text .= "| ";
                                if (AC_GetLoggingStatus($this->ipsArchiveControlId, $value3) == 1) {
                                    if (AC_GetAggregationType($this->ipsArchiveControlId, $value3) == 0) {
                                        $text .= $this->Translate("yes") . " (" . $this->Translate("standard") . ")";
                                    } else {
                                        $text .= $this->Translate("yes") . " (" . $this->Translate("counter") . ")";
                                    }
                                }

                                $text .= "| " . $this->removeTagsFromText(IPS_GetObject($value3)['ObjectInfo']) . PHP_EOL;
                            }
                        }

                        if (($typeName == $this->Translate("LINK")) && ($this->ReadPropertyBoolean("overviewLinks"))) {
                            $text .= "### " . $tagName . " (" . $typeName . ")" . PHP_EOL;
                            $text .= "| Id";
                            $text .= " | " . $this->Translate("name and location");
                            $text .= " | " . $this->Translate("linked object");
                            $text .= " | " . $this->Translate("description") . PHP_EOL;
                            $text .= "| --- | --- | --- | --- |" . PHP_EOL;
        
                            
                            foreach ($this->tagList[$tagName][$typeName] as $key3 => $value3) {
                                $text .= "| " . $value3;
                                $text .= "| " . IPS_GetLocation($value3);
                                $targetId = IPS_GetLink($value3)['TargetID'];
                                if (IPS_ObjectExists($targetId)) {
                                    $text .= "| " . IPS_GetLocation($targetId) . " ([" . $targetId . "](./details/" . $targetId . ".md))";
                                }
                                $text .= "| " . $this->removeTagsFromText(IPS_GetObject($value3)['ObjectInfo']) . PHP_EOL;
                            }
                        }


                        if (($typeName == $this->Translate("EVENT")) && ($this->ReadPropertyBoolean("overviewEvent"))) {
                            $text .= "### " . $tagName . " (" . $typeName . ")" . PHP_EOL;
                            $text .= "| Id";
                            $text .= " | " . $this->Translate("name and location");
                            $text .= " | " . $this->Translate("number of childs");
                            $text .= " | " . $this->Translate("number of references");
                            $text .= " | " . $this->Translate("event has conditions");
                            $text .= " | " . $this->Translate("event type");
                            $text .= " | " . $this->Translate("description") . PHP_EOL;
                            $text .= "| --- | --- | --- | --- | --- | --- | --- |" . PHP_EOL;
        
                            
                            foreach ($this->tagList[$tagName][$typeName] as $key3 => $value3) {
                                $text .= "| [" . $value3 . "](./details/" . $value3 . ".md)";
                                $text .= "| " . IPS_GetLocation($value3);
                                $text .= "| " . count(IPS_GetChildrenIDs($value3));
                                $text .= "| " . count(UC_FindReferences($this->ipsUtilControlId, $value3));
                                
                                $text .= "| ";
                                if (count(IPS_GetEvent($value3)['EventConditions'])>0) {
                                    $text .= $this->Translate("yes");
                                }
                                
                                $text .= "| " . $this->ipsEventType[IPS_GetEvent($value3)['EventType']];
                                $text .= "| " . $this->removeTagsFromText(IPS_GetObject($value3)['ObjectInfo']) . PHP_EOL;
                            }
                        }

                        if (($typeName == $this->Translate("INSTANCE")) && ($this->ReadPropertyBoolean("overviewInstances"))) {
                            $text .= "### " . $tagName . " (" . $typeName . ")" . PHP_EOL;
                            $text .= "| Id";
                            $text .= " | " . $this->Translate("name and location");
                            $text .= " | " . $this->Translate("instance status");
                            $text .= " | " . $this->Translate("description") . PHP_EOL;
                            $text .= "| --- | --- | --- | --- |" . PHP_EOL;
        
                            
                            foreach ($this->tagList[$tagName][$typeName] as $key3 => $value3) {
                                $text .= "| [" . $value3 . "](./details/" . $value3 . ".md)";
                                $text .= "| " . IPS_GetLocation($value3);
                                $text .= "| " . $this->ipsInstanceStatus[IPS_GetInstance($value3)['InstanceStatus']];
                                $text .= "| " . $this->removeTagsFromText(IPS_GetObject($value3)['ObjectInfo']) . PHP_EOL;
                            }
                        }


                        if (($typeName == $this->Translate("MEDIA")) && ($this->ReadPropertyBoolean("overviewMedia"))) {
                            $text .= "### " . $tagName . " (" . $typeName . ")" . PHP_EOL;
                            $text .= "| Id";
                            $text .= " | " . $this->Translate("name and location");
                            $text .= " | " . $this->Translate("media file");
                            $text .= " | " . $this->Translate("media type");
                            $text .= " | " . $this->Translate("media size");
                            $text .= " | " . $this->Translate("description") . PHP_EOL;
                            $text .= "| --- | --- | --- | --- | --- | ---- |" . PHP_EOL;
        
                            
                            foreach ($this->tagList[$tagName][$typeName] as $key3 => $value3) {
                                $text .= "| [" . $value3 . "](./details/" . $value3 . ".md)";
                                $text .= "| " . IPS_GetLocation($value3);
                                $text .= "| " . IPS_GetMedia($value3)['MediaFile'];
                                $text .= "| " . $this->ipsMediaType[IPS_GetMedia($value3)['MediaType']];
                                $text .= "| " . IPS_GetMedia($value3)['MediaSize'];
                                $text .= "| " . $this->removeTagsFromText(IPS_GetObject($value3)['ObjectInfo']) . PHP_EOL;
                            }
                        }
                    }
                }
            }
            return $text;
        }

        // ====================================
        // END PREPARATION OF OVERVIEW SECTIONS
        // ====================================

        /**
         * Create the required directories
         *
         * @return void
         */
        private function createDir()
        {
            if (!is_dir($this->outputFolderOverview)) {
                mkdir($this->outputFolderOverview);
            }

            if (!is_dir($this->outputFolderDetails)) {
                mkdir($this->outputFolderDetails);
            }
        }

        /**
         * Shows a list of all used tags withing object infos.
         *
         * @return string List of used tags including crlf
         */
        public function ListTags()
        {
            $this->setConsts();
            $this->genOverviewArray();

            echo "List of used tags: \n";
            foreach ($this->tagList as $key => $value) {
                echo $key . PHP_EOL;
            }
        }

        /**
         * Main method which generates the documentation
         *
         * @return void
         */
        public function WriteMd()
        {
            $this->setConsts();
            $this->createDir();

            // Generate all detail files
            $this->createVariableFiles();
            $this->createScriptFiles();
            $this->createEventFiles();
            $this->createInstanceFiles();
            $this->createMediaFiles();

            // Prepare array for overview and generate page
            $this->genOverviewArray();
            $text = "";
            $text .= $this->overviewHeader();
            
            if ($this->ReadPropertyBoolean("overviewGeneralInfos")) {
                $text .= $this->overviewGenericInfo();
            }
            
            if ($this->ReadPropertyBoolean("overviewExtProperties")) {
                $text .= $this->overviewExtProps();
            }
            
            $text .= $this->genToc();
            $text .= $this->genOverviewByTag();

            $text .= $this->overviewFooter();
            file_put_contents($this->outputFolderOverview . "/index.md", utf8_decode($text) . PHP_EOL);

            SetValue($this->GetIDForIdent("SymDoc_LastExec"), date("d.m.Y H:i"));
            echo $this->Translate("Documentation has been created");
        }

        /**
         * This functions sets object infos text recursive starting at parentid.
         *
         * @param  mixed $parentId starting id
         * @param  mixed $description object info text including tags
         * @param  mixed $appendInfo true when description should be appended to existing text
         *
         * @return void
         */
        public function WriteRecursiveObjInfo($parentId, $description, $appendInfo=true)
        {
            $tmp1 = $this->GetRecursiveObjectList($parentId);

            foreach ($tmp1 as $tmp2) {
                if ($appendInfo) {
                    IPS_SetInfo($tmp2, IPS_GetObject($tmp2)['ObjectInfo'] . " " . $description);
                } else {
                    IPS_SetInfo($tmp2, $description);
                }
            }
        }
        
        /**
         * GetRecursiveObjectList
         *
         * @param  mixed $parent
         *
         * @return array List of all childs ids (recursive) under parentId
         */
        private function GetRecursiveObjectList($parent)
        {
            $ids = IPS_GetChildrenIDs($parent);
            foreach ($ids as $id) {
                $ids = array_merge($ids, $this->GetRecursiveObjectList($id));
            }
            return $ids;
        }



        /**
         * Generated md syntax text with common object information using IPS_GetObject
         *
         * @param  int $id object id
         *
         * @return string md syntax with common object information
         */
        private function getObjectHeader($id)
        {
            $erg = IPS_GetObject($id);

            $text = "# " . $this->ipsObjectType[$erg['ObjectType']] . ": ";
            $text .= IPS_GetName($id) . " (" . $id . ")" . PHP_EOL;
            $text .= "### " . IPS_GetLocation($id) . PHP_EOL;

            $text .= "> [" . $this->Translate("Back to overview") . "](../index.md)" . PHP_EOL . PHP_EOL;

            $text .= "### " . $this->Translate("Common object information") . PHP_EOL;
            $text .= "* " . $this->Translate("object icon") . ": " . $erg['ObjectIcon'] . PHP_EOL;

            $text .= "* " . $this->Translate("object ident") . ": " . $erg['ObjectIdent'] . PHP_EOL;
            $text .= "* " . $this->Translate("object info") . ": **" . $erg['ObjectInfo'] . "**" . PHP_EOL;
            
            if ($erg['ObjectIsDisabled'] == 1) {
                $text .= "* " . $this->Translate("is object disabled?") . ": " . $this->Translate("yes") . PHP_EOL;
            } else {
                $text .= "* " . $this->Translate("is object disabled?") . ": " . $this->Translate("no") . PHP_EOL;
            }

            $text .= $this->getRefsFromId($id) . PHP_EOL . PHP_EOL;

            $text .= "### " . $this->Translate("child elements") . PHP_EOL;
            $childIds = IPS_GetChildrenIDs($id);

            $text .= "| " . $this->Translate("id");
            $text .= " | " . $this->Translate("object Type");
            $text .= " | " . $this->Translate("object name");
            $text .= " | " . $this->Translate("object description") . PHP_EOL;
            $text .= "| --- | --- | --- | --- |" . PHP_EOL;
            
            foreach ($childIds as $key => $val) {
                $text .= "| [" . $val . "](" .  $val . ".md)";
                $text .= "| " . $this->ipsObjectType[IPS_GetObject($val)['ObjectType']];
                $text .= "| " . IPS_GetName($val);
                $text .= "| " . IPS_GetObject($val)['ObjectInfo'] . PHP_EOL;
            }

            $text .= PHP_EOL . "---" . PHP_EOL;

            return $text;
        }

        private function genToc()
        {
            $tocObject = PHP_EOL . "# " . $this->Translate("toc") . PHP_EOL;
            $tocObject .= "<details><summary>" . $this->Translate("toc of symcon objects") . "</summary>" . PHP_EOL;
            $tocObject .= "<p>" . PHP_EOL . PHP_EOL;
            ksort($this->tagList);
            foreach ($this->tagList as $key => $value) {
                $tocObject .=" * [" . $key . "](#" . strtolower($key) . ")" . PHP_EOL;
            }
            $tocObject .= PHP_EOL . PHP_EOL;
            $tocObject .= "</p>" . PHP_EOL . "</details>" . PHP_EOL . PHP_EOL;
        
            return $tocObject;
        }

        /**
         * Generates a md table with referenced objects
         *
         * @param  int id
         *
         * @return string md formatted text with referenced objects
         */
        private function getRefsFromId($id)
        {
            $erg = UC_FindReferences($this->ipsUtilControlId, $id);

            $text = "### " . $this->Translate("referenced objects") . PHP_EOL;

            $text .= "| " . $this->Translate("id");
            $text .= " | " . $this->Translate("name and location");
            $text .= " | " . $this->Translate("reference type") . PHP_EOL;
            $text .= "| --- | --- | --- |" . PHP_EOL;
  
            foreach ($erg as $entry) {
                $objId = $entry['ObjectID'];
                $text .= "| [" . $objId . "](" . $objId . ".md)";
                $text .= "| " . IPS_GetName($objId) . " (" . IPS_GetLocation($objId) . ")";
                $text .= "| " . $this->ipsObjectType[IPS_GetObject($objId)['ObjectType']] . PHP_EOL;
            }
            
            return $text;
        }

        /**
         * getLinksFromId
         *
         * @param  mixed $id
         *
         * @return void
         */
        private function getLinksFromId($id)
        {
            $links = array();
            $erg = IPS_GetLinkList();
            foreach ($erg as $linkId) {
                $tmp = IPS_GetLink($linkId);
                if ($tmp['TargetID'] == $id) {
                    array_push($links, $linkId);
                }
            }

            return $links;
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

            if (strlen($text) == 0) {
                // Text is empty
                array_push($erg, "UNTAGGED");
            } else {
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
            if ($this->ReadPropertyBoolean("overviewRemoveDescTags")) {
                return preg_replace("/#(\w+)/", "", $text);
            } else {
                return $text;
            }
        }

        /**
         * genOverviewArray
         *
         * @return void
         */
        private function genOverviewArray()
        {
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
        }
    }
