MinyGUI gui;

String Rmoteur, Rdirection;
MinyInteger caption, direction, moteur, vitesse;
MinyString  blank, success;
 
//Actions
void mousePressed() {gui.onMousePressed();}
void keyPressed()   {gui.onKeyPressed();  }

void setup()
{
  size(200, 250); // Taille fenêtre. largeur x Longeur
  
  direction = new MinyInteger(0);
  caption   = new MinyInteger(10);
  moteur    = new MinyInteger(0);  
  vitesse   = new MinyInteger(0);  
  success   = new MinyString(" "); 
  blank     = new MinyString(" ");
 
  gui = new MinyGUI(0, 20, 170, height);
  gui.addDisplay(" ", blank);
  gui.addList("Moteur", moteur, "  x;  y;  z");
  gui.addSlider("Vitesse", vitesse, 0, 99);
  gui.addDisplay(" ", vitesse);
  gui.addDisplay(" ", blank);
  gui.addList("Direction", direction, "        Avant;        Arrière");
  gui.addDisplay(" ", blank);
  gui.addEditBox("Nb. Pas: ", caption);
  gui.addDisplay(" ", blank);
  gui.addButton("Envoyer", new ButtonRandomize());
  
  gui.fg = color(255);
  gui.bg = color(126);
 
}

class ButtonRandomize implements ButtonCallback
{
  void onButtonPressed()
  { 
       if(moteur.getValue() == 0){Rmoteur = "x";}
  else if(moteur.getValue() == 1){Rmoteur = "y";}
  else if(moteur.getValue() == 2){Rmoteur = "z";}
                 else {Rmoteur = "ERR_MOT";}
                 
       if(direction.getValue() == 0){Rdirection =  "+";} // + -  (Bug)
  else if(direction.getValue() == 1){Rdirection = "-";}
                 else {Rdirection = "ERR_DIR";}
 
  javax.swing.JOptionPane.showMessageDialog(null,"Envoyé: "+Rmoteur+Rdirection+vitesse.getValue()); 
 
  }
}

void draw()
{
  background(0);
  fill(16); noStroke();
  gui.display();
  textAlign(CENTER);
}
