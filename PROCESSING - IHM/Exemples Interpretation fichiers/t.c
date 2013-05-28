/*   ___     ___     ___     ___     ___     ___     ___     ___
 ___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/   \___
/   \___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/   \
\___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/
/   \___/                                                   \___/   \
\___/       Paul FASOLA                           2012-2013     \___/
/   \                    Interface Homme-Machine                /   \
\___/                                                           \___/
/   \___                  via Processing (GUI)               ___/   \
\___/   \___     ___     ___     ___     ___     ___v 1.6___/   \___/
/   \___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/   \
\___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/
    \___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/
*/


/*
** INCLUDES
*/
import javax.swing.JOptionPane; //Pour les messages (Erreur/Succes)
import processing.serial.*;  
import javax.swing.*; 

/*
** DECLARATIONS
*/
Boolean debugMode = false; // Active le mode debug (Désactive la liaison arduino)
Boolean passed = true; // Pour la validation du formulaire
String Rmoteur, Rdirection, Pas, hexVitesse, error = "Aucune info";
int[] savedValue = {0,0,0};
int intMoteur;
MinyInteger caption, direction, moteur, vitesse;
MinyString  blank, success, x, y, z, sep;
 
Serial arduino; 

/*
** APPEL DE CLASSES
*/
MinyGUI gui; // Classe pour la partie garphique
PFont f;     // Classe de définition des polices.

 
// ACTIONS

void mousePressed() {gui.onMousePressed();} // Récupere et traite les evenements (callbacks) d'un clic.
void   keyPressed() {gui.onKeyPressed();  } // Si une touche est apuyée, on exécute la fonction appellée en callback

void setup()
{
  f = createFont("Segoe UI Semibold", 12); // Police métro
  textFont(f);
  println(Serial.list()); // Affiche le port utilisé par Arduino (pour debugs éventuels.)
  size(200, 375); // Taille fenêtre. largeur x Longeur
  
 // On instancie
  
  direction = new MinyInteger(0);
  caption   = new MinyInteger(100);
  moteur    = new MinyInteger(0);  
  vitesse   = new MinyInteger(60);  
  success   = new MinyString(" "); 
  blank     = new MinyString(" ");
  x         = new MinyString("0");
  y         = new MinyString("0");
  z         = new MinyString("0");
  sep       = new MinyString("______________");
  if(!debugMode)
 {arduino   = new Serial(this, Serial.list()[0], 9600);}
  gui       = new MinyGUI(15, 0, 170, height);

  // Génération du formulaire
  gui.addDisplay("X", x);
  gui.addDisplay("Y", y);
  gui.addDisplay("Z", z);
  gui.addDisplay("______________", sep);
  gui.addDisplay(" ", blank);
  gui.addList("Moteur", moteur, "  x;  y;  z");
  gui.addSlider("Vitesse", vitesse, 0, 99);
  gui.addDisplay(" ", vitesse);
  gui.addDisplay(" ", blank);
  gui.addList("Direction", direction, "        Avant;        Arrière");
  gui.addDisplay(" ", blank);
  gui.addEditBox("Nb. Pas: ", caption);
  gui.addDisplay(" ", blank);
  gui.addButton("Envoyer", new sendForm());
  gui.addDisplay(" ", blank);
  gui.addButton("Retour Origine", new toOrigin());
  gui.addDisplay(" ", blank);
  gui.addButton("Interpreteur fichier", new interpreter());
  gui.fg = color(255);
  gui.bg = color(126);
 
 javax.swing.JOptionPane.showMessageDialog(null, "Procèdez à la calibration", "Calibration", JOptionPane.INFORMATION_MESSAGE);
}

class sendForm implements ButtonCallback // Détecte un clic sur le bouton qui valide le formulaire
{
  
/*
** FONCTION onButtonPressed : Callback du segment Envoyer.
** @params : void
** @output : hybrid
*/

  void onButtonPressed()
  {
  println("X : " + savedValue[0]);
  println("Y : " + savedValue[1]);
  println("Z : " + savedValue[2]);
  
/*
** PARTIE VERIFICATION FORMULAIRE
*/
    
       if(moteur.getValue() == 0){Rmoteur = "x"; intMoteur = 0;}
  else if(moteur.getValue() == 1){Rmoteur = "y"; intMoteur = 1;}
  else if(moteur.getValue() == 2){Rmoteur = "z"; intMoteur = 2;}
  else {Rmoteur = "ERR_MOT"; passed = false;} // On indique une erreur et on l'explique.
                 
       if(direction.getValue() == 0){Rdirection =  "1";} // On recule
  else if(direction.getValue() == 1){Rdirection = "0";}  // On avance
  else {Rdirection = "ERR_DIR"; passed = false;}
                 
        if(vitesse.getValue() == 0){passed=false; error="La vitesse ne peut pas être nulle.";}
                 
                 
                 if(passed) // Si pas d'erreurs
                 {
                   String toSend = "mot "+Rmoteur+Rdirection+caption.getValue()+"v"+vitesse.getValue(); // On compose la chaine
                  
                   if(envoieInstructionSerie(toSend)) // On envoie, on dit que tout est bon
                   {
                       if(Integer.parseInt(Rdirection) == 1)
                     {
                       savedValue[intMoteur] += caption.getValue();
                     }
                     else
                     {
                      savedValue[intMoteur] -= caption.getValue();
                     }  
                     
                       if(Rmoteur == "x")
                       {
                         x.setValue(Integer.toString(savedValue[intMoteur]));
                       }
                       else if(Rmoteur == "y")
                       {
                         y.setValue(Integer.toString(savedValue[intMoteur]));
                       }
                       else if(Rmoteur == "z")
                       {
                        z.setValue(Integer.toString(savedValue[intMoteur]));
                       }
                       delay(20);
                       javax.swing.JOptionPane.showMessageDialog(null, "Envoyé: "+Rmoteur+Rdirection+caption.getValue()+"v"+ vitesse.getValue()+" ( Hex : "+Rmoteur+Rdirection+caption.getValue()+"v"+vitesse.getValue()+")", "Succès !", JOptionPane.INFORMATION_MESSAGE);
                  } 
                   else {passed=false; error="La commande n'a pas pu être envoyée.";}
                 }
                 else // Sinon, on a passed = false, on indique l'erreur et on repasse passed = true
                 {
                   javax.swing.JOptionPane.showMessageDialog(null, "Erreur(s) dans le Formulaire: "+error, "Erreur !", JOptionPane.ERROR_MESSAGE);
                   error = "";
                   passed = true;
                 }
 if(debugMode)
 {
  println("X : " + savedValue[0]);
  println("Y : " + savedValue[1]);
  println("Z : " + savedValue[2]);
 }
 
  }
}

class toOrigin implements ButtonCallback 
{
  
/*
** FONCTION onButtonPressed : Callback du segment Retour Origine.
** @params : void
** @output : hybrid
*/
  void onButtonPressed()
  {
    int _valX, _valY, _valZ; // Variables locales
    
    _valX = savedValue[0];
    _valY = savedValue[1];
    _valZ = savedValue[2];
    
    if(_valX < 0)
    {
     _valX = - _valX; // Valeur absolue pour une distance à parcourir (en tours)
     delay(20);envoieInstructionSerie("mot x1"+_valX+"v60"); // V60 +> Retour rapide
    }
      else{delay(20);envoieInstructionSerie("mot x0"+_valX+"v60");}
     
     println("X OK");
     if(_valY < 0)
    {
     _valY = - _valY; // Valeur absolue pour une distance à parcourir (en tours)
     delay(20);envoieInstructionSerie("mot y1"+_valY+"v60"); // V60 +> Retour rapide
    }
      else{delay(20);envoieInstructionSerie("mot y0"+_valY+"v60");}
      println("Y OK");
     if(_valZ < 0)
    {
     _valZ = - _valZ; // Valeur absolue pour une distance à parcourir (en tours)
     delay(20);envoieInstructionSerie("mot z1"+_valZ+"v60"); // V60 +> Retour rapide
    }
      else{delay(20);envoieInstructionSerie("mot z0"+_valZ+"v60");}
    println("Z OK");
    
    
    x.setValue(Integer.toString(savedValue[0]=0));
    y.setValue(Integer.toString(savedValue[1]=0));
    z.setValue(Integer.toString(savedValue[2]=0));
    
     javax.swing.JOptionPane.showMessageDialog(null, "Retour origine programmé.", "Retour Origine - [OK]", JOptionPane.INFORMATION_MESSAGE);
  }

}

class interpreter implements ButtonCallback 
{
  
/*
** FONCTION onButtonPressed : Callback du segment Interpretation fichier.
** @params : void
** @output : hybrid
*/
  void onButtonPressed()
  {
    
try { 
  UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName()); 
} catch (Exception e) { 
  e.printStackTrace();  
 
} 
 
final JFileChooser fc = new JFileChooser(); 
int returnVal = fc.showOpenDialog(null);
 
if (returnVal == JFileChooser.APPROVE_OPTION) { 
  File file = fc.getSelectedFile(); 

  if (file.getName().endsWith("gcode") || file.getName().endsWith("txt") || file.getName().endsWith("sh")) { 
 
    String lines[] = loadStrings(file); 
    for (int i = 0; i < lines.length; i++) {
 
      if(lines[i].indexOf("m") == 0)
      {
      String moteur =  lines[i].substring(4,5);
      String dir =     lines[i].substring(5,6);
      String pas =     lines[i].substring(6,lines[i].indexOf("v"));
      String vitesse = lines[i].substring(lines[i].indexOf("v")+1,lines[i].length());
      delay(10);
      envoieInstructionSerie("mot" + moteur + dir + pas + "v" + vitesse);
      javax.swing.JOptionPane.showMessageDialog(null, "Ligne "+i+" -> Commande envoyée (mot"+ moteur + dir + pas + "v" + vitesse+")", "Interpreteur de fichier", JOptionPane.INFORMATION_MESSAGE);
      
      println("**************** INSTRUCTION "+i+" ****************");
      println("   Moteur : "+moteur);
      println("Direction : "+dir);
      println("      Pas : "+pas);
      println("  Vitesse : "+vitesse);
      println("***********************************************");
      println(" ");
  } 
    else if(lines[i].indexOf("#") == 0)
    {
      println(" ");
      println("--->                                                                    COMMENTAIRE DE LA LIGNE n°"+i+" : "+lines[i].substring(1));
      println(" ");
    }
       else if(lines[i].indexOf("o") == 0)
    {
      
      println(" ");
      println("--->                                                                    ** Retour Origine de tous les moteurs **");
      retournerOrigine();
      println(" ");
    }
    
  
    }
  
} 
else
{
  javax.swing.JOptionPane.showMessageDialog(null, "Extension de fichier invalide (.tatav/.camcam/.txt)", "Extension invalide", JOptionPane.INFORMATION_MESSAGE);
}
  javax.swing.JOptionPane.showMessageDialog(null, "Fin d'interpretation.", "Interpretation fichier [OK]", JOptionPane.INFORMATION_MESSAGE);
  }
  else {
        
  }
}
  }



/*
** FONCTION draw : DEPLOYEMENT DE LA PARTIE GUI
** @params : void
** @output : hybrid
*/
void draw()
{
  background(0);
  fill(16); noStroke();
  gui.display();
  textAlign(CENTER);
}

/*
** FONCTION draw : ENVOIE DONNEES SERIE
** @params : String
** @output : Boolean
*/
public boolean envoieInstructionSerie(String instruction)
{  
  
    if(!debugMode)
    {
        arduino.write(instruction);
        delay(700); // tester delai +/-
    }
 
     return true;
}

void retournerOrigine()
{
     int _valX, _valY, _valZ; // Variables locales
    
    _valX = savedValue[0];
    _valY = savedValue[1];
    _valZ = savedValue[2];
    
    if(_valX < 0)
    {
     _valX = - _valX; // Valeur absolue pour une distance à parcourir (en tours)
     delay(20);envoieInstructionSerie("mot x1"+_valX+"v60"); // V60 +> Retour rapide
    }
      else{delay(20);envoieInstructionSerie("mot x0"+_valX+"v60");}
     
     println("X retourne à l'origine [OK]");
     if(_valY < 0)
    {
     _valY = - _valY; // Valeur absolue pour une distance à parcourir (en tours)
     delay(20);envoieInstructionSerie("mot y1"+_valY+"v60"); // V60 +> Retour rapide
    }
      else{delay(20);envoieInstructionSerie("mot y0"+_valY+"v60");}
      println("Y retourne à l'origine [OK]");
     if(_valZ < 0)
    {
     _valZ = - _valZ; // Valeur absolue pour une distance à parcourir (en tours)
     delay(20);envoieInstructionSerie("mot z1"+_valZ+"v60"); // V60 +> Retour rapide
    }
      else{delay(20);envoieInstructionSerie("mot z0"+_valZ+"v60");}
    println("Z retourne à l'origine [OK]");
    
}