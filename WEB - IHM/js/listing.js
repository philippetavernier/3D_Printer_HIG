
    $(function() {
        var typeJumpTags = [
            "Base Jump",
            "Brevet A",
            "Brevet B",
            "Brevet B1",
            "Brevet B2",
            "Brevet B3",
            "Brevet B4",
            "Brevet B4i",
            "Brevet C",
            "Canopy Piloting",
            "Chute assise",
            "Freestyle",
            "Free Fly",
            "Précision d'atterrissage",
            "Saut fun",
            "Sky surf",
            "Tandem",
            "Voltige",
            "Vol relatif",
            "Voile contact",
            "VRV",
            "Wing Suit"
        ];
         
var typeDzTags = [
"Aérodrome de Champ Cadet - Château-Thierry (02400)",
"Centre de parachutisme Lapalisse-Aéro - Lapalisse (03120)",
"Centre Ecole Régional de Parachutisme Sportif - Tallard (05130)",
"Aérodrome de Ardennes Tournes (08090)",
"Aérodrome de Pamiers les Pujol - Pamiers (09101)", 
"Centre de Parachutisme de Brienne le Château - Brienne le Château (10500)",
"Parachute Club d'Aix - Ecole du grand St-Jean - Aix en Provence (13100)",
"Aérodrome de Royan-Medis - Médis (17600)",
"Ecole de Parachutisme du Valinco",
"Aérodrome de Propriano-Tavaria - Propriano (20110)",
"Aérodrome de Bastia Poretta - Bastia (20200)",
"Aérodrome de Gueret-St Laurent - Gueret (23000 )",
"Aérodrome de Roumanière - Bergerac (24100)",
"Aérodrome de Courcelles les Montbéliard - Courcelles les Montbéliard (25420)",
"Aérodrome de Besançon La Vèze - La Vèze (25660)",
"Aérodrome de Pujaut - Pujaut (30131)",
"Paraclub du Gard - Montpezat (30730)",
"Aérodrome Villemarie La Teste (33260)",
"Aérodrome de Soulac sur Mer - Soulac/Mer (33780)",
"Aérodrome de Le Blanc - Le Blanc (36300)",
"Aérodrome de Grenoble St Geoirs - St-Etienne de St-Geoirs (38590)",
"Aérodrome de Mimizan - Mimizan Cedex (40201)",
"Aérodrome de St Galmier - St Galmier (42330)",
"Aérodrome de Orléans - St-Denis de l'Hôtel (45550)",
"Aérodrome de Cahors-Lalbenque - Cieurac (46230)",
"Aérodrome Agen La Garenne - Le Passage d'Agen (47520)",
"Aérodrome de Terrefort - Saint-Hilaire Saint-Florent",
"Aérodrome de Bréville sur Mer - Granville (50000)",
"CERP de Champagne - Mourmelon cedex (51401)",
"Centre Ecole de la Mayenne - Laval (53000)",
"Aérodrome de Nancy-Azelot - Saint Nicolas de Port (54210)",
"Ecole de Parachutisme Sportif de la Moselle - Doncourt les Conflans (54800)",
"Aérodrome de Vannes-Meucon -  Monterblanc (56250)",
"Aérodrome de Prouvy Valencienne - Prouvy (59121)",
"Aérodrome de la Salmagne - Vieux Reng (59600)",
"Aérodrome de Lille-Marcq RN 17 - Bondues (59510 )",
"Aérodrome de Frétoy le Château - Frétoy le Château (60640)",
"Aérodrome de Lens-Benifontaine - Loos en Gohelle (62750)",
"Aérodrome de Lasclaveries - Lasclaveries (64450)",
"Centre de parachutisme de Tarbes - Laloubère (65310)",
"Aérodrome du Polygon Strasbourg (67100)",
"Ecole de Parachutisme Sportif de Colmar - Colmar - Houssen (68000)",
"Aérodrome de Corbas - Corbas (69960)",
"Aérodrome de Vesoul - Vesoul (70000)",
"Aérodrome de Champforgueil - Châlon sur Saone (71530)",
"Air Libre Parachutisme - Cherré (72400)",
"Aéroport de Chambéry - Le Viviers du Lac (73420)",
"Aérodrome Route de Thonon - Annemasse (74100)",
"Aérodrome de Dieppe, St Aubin/Scie - Offranville (76550)",
"Aérodrome de Peronne - Peronne (80200)",
"Aérodrome de Bouloc - Bouloc (82110)",
"Aérodrome du Luc - Le Cannet des Maures (83340)",
"Aérodrome de Cheu - St-Florentin (89600)",
"Aéroparc de Belfort - Fontaine (90150)",
"Aérodrome du Raizet - St François (97118)",
"Paraclub de la Martinique - Lamentin (97232)",
"Aérodrome de Pierrefonds - St-Pierre Cedex (97453)",
"CEPS Tony Dell'aquila - Djibouti",
"Aérodrome de Magenta - Noumea" ];

 var typeAeronefTags = [
            "Pilatus PC-6 Porter",
            "ULM",
            "Brevet B"
        ];
        $( "#jumpTags" ).autocomplete({
            source:  typeJumpTags
        });
        $( "#aeronefTags" ).autocomplete({
            source: typeAeronefTags
        });
         $( "#dzTags" ).autocomplete({
            source: typeDzTags
        });
    });