import processing.core.*; 
import processing.data.*; 
import processing.event.*; 
import processing.opengl.*; 

import javax.swing.JOptionPane; 
import processing.serial.*; 

import java.util.HashMap; 
import java.util.ArrayList; 
import java.io.File; 
import java.io.BufferedReader; 
import java.io.PrintWriter; 
import java.io.InputStream; 
import java.io.OutputStream; 
import java.io.IOException; 

public class IHM extends PApplet {

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
 //Pour les messages (Erreur/Succes)
     

/*
** DECLARATIONS
*/
Boolean debugMode = true; // Active le mode debug (D\u00e9sactive la liaison arduino)
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
PFont f;     // Classe de d\u00e9finition des polices.

 
// ACTIONS

public void mousePressed() {gui.onMousePressed();} // R\u00e9cupere et traite les evenements (callbacks) d'un clic.
public void   keyPressed() {gui.onKeyPressed();  } // Si une touche est apuy\u00e9e, on ex\u00e9cute la fonction appell\u00e9e en callback

public void setup()
{
  f = createFont("Segoe UI Semibold", 12); // Police m\u00e9tro
  textFont(f);
  println(Serial.list()); // Affiche le port utilis\u00e9 par Arduino (pour debugs \u00e9ventuels.)
  size(200, 325); // Taille fen\u00eatre. largeur x Longeur
  
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

  // G\u00e9n\u00e9ration du formulaire
  gui.addDisplay("X", x);
  gui.addDisplay("Y", y);
  gui.addDisplay("Z", z);
  gui.addDisplay("______________", sep);
  gui.addDisplay(" ", blank);
  gui.addList("Moteur", moteur, "  x;  y;  z");
  gui.addSlider("Vitesse", vitesse, 0, 99);
  gui.addDisplay(" ", vitesse);
  gui.addDisplay(" ", blank);
  gui.addList("Direction", direction, "        Avant;        Arri\u00e8re");
  gui.addDisplay(" ", blank);
  gui.addEditBox("Nb. Pas: ", caption);
  gui.addDisplay(" ", blank);
  gui.addButton("Envoyer", new sendForm());
  gui.addDisplay(" ", blank);
  gui.addButton("Retour Origine", new toOrigin());
  gui.fg = color(255);
  gui.bg = color(126);
 
 javax.swing.JOptionPane.showMessageDialog(null, "Proc\u00e8dez \u00e0 la calibration", "Calibration", JOptionPane.INFORMATION_MESSAGE);
}

class sendForm implements ButtonCallback // D\u00e9tecte un clic sur le bouton qui valide le formulaire
{
  
/*
** FONCTION onButtonPressed : Callback du segment Envoyer.
** @params : void
** @output : hybrid
*/

  public void onButtonPressed()
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
                 
        if(vitesse.getValue() == 0){passed=false; error="La vitesse ne peut pas \u00eatre nulle.";}
                 
                 
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
                       javax.swing.JOptionPane.showMessageDialog(null, "Envoy\u00e9: "+Rmoteur+Rdirection+caption.getValue()+"v"+ vitesse.getValue()+" ( Hex : "+Rmoteur+Rdirection+caption.getValue()+"v"+vitesse.getValue()+")", "Succ\u00e8s !", JOptionPane.INFORMATION_MESSAGE);
                  } 
                   else {passed=false; error="La commande n'a pas pu \u00eatre envoy\u00e9e.";}
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
  public void onButtonPressed()
  {
    int _valX, _valY, _valZ; // Variables locales
    
    _valX = savedValue[0];
    _valY = savedValue[1];
    _valZ = savedValue[2];
    
    if(_valX < 0)
    {
     _valX = - _valX; // Valeur absolue pour une distance \u00e0 parcourir (en tours)
     delay(20);envoieInstructionSerie("mot x1"+_valX+"v60"); // V60 +> Retour rapide
    }
      else{delay(20);envoieInstructionSerie("mot x0"+_valX+"v60");}
     
     println("X OK");
     if(_valY < 0)
    {
     _valY = - _valY; // Valeur absolue pour une distance \u00e0 parcourir (en tours)
     delay(20);envoieInstructionSerie("mot y1"+_valY+"v60"); // V60 +> Retour rapide
    }
      else{delay(20);envoieInstructionSerie("mot y0"+_valY+"v60");}
      println("Y OK");
     if(_valZ < 0)
    {
     _valZ = - _valZ; // Valeur absolue pour une distance \u00e0 parcourir (en tours)
     delay(20);envoieInstructionSerie("mot z1"+_valZ+"v60"); // V60 +> Retour rapide
    }
      else{delay(20);envoieInstructionSerie("mot z0"+_valZ+"v60");}
    println("Z OK");
    
    
    x.setValue(Integer.toString(savedValue[0]=0));
    y.setValue(Integer.toString(savedValue[1]=0));
    z.setValue(Integer.toString(savedValue[2]=0));
    
     javax.swing.JOptionPane.showMessageDialog(null, "Retour origine programm\u00e9.", "Retour Origine - [OK]", JOptionPane.INFORMATION_MESSAGE);
  
  }

}

/*
** FONCTION draw : DEPLOYEMENT DE LA PARTIE GUI
** @params : void
** @output : hybrid
*/
public void draw()
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
class MinyGUI
{
  private int _x, _y, _w, _h, _totalH;
  int bg, fg, selectColor;
  private ArrayList properties;
  private MinyWidget locked;
  private VScrollbar scrollbar;
  
  MinyGUI(int x, int y, int w, int h) 
  {
    _x = x;
    _y = y;
    _w = w;
    _h = h;
    _totalH = 0;
    
    locked = null;
    bg = color(128);
    fg = color(0);
    selectColor = color(96);
    
    properties = new ArrayList();
  }
  
  private void update()
  {
    if(scrollbar != null)
      scrollbar.update();
      
    if(locked != null)
      locked.update();
    
    if(!overRect(_x, _y, _w, _h))
      return;
    
    // its value can have changed
    if(locked == null)
    {
      for(int i=0; i<properties.size(); i++)
        ((Property)properties.get(i)).update();
    }
  }
  
  public void getLock(MinyWidget p)
  {
    if(locked == null)
      locked = p;
  }
  
  public boolean hasLock(MinyWidget p)
  {
    return (locked==p);
  }
  
  public void releaseLock(MinyWidget p) 
  {  
    if(locked == p)
      locked = null; 
  }
  
  public void display()
  {
    update();
    
    if(scrollbar != null)
    {
      scrollbar.display();
      float y = _y+min(0, -(_totalH - _h) * scrollbar.pos);
      for(int i=0; i<properties.size(); i++)
      {
        Property p = (Property)properties.get(i);
        Rect r = p.getRect();
        p.setPosition(r._x, (int)y, r._w);
        int h = p.getHeight();
        if(y+h > _y && y<_y+_h)
          p.display();
        y += h;
      }
    }
    else
    {
      for(int i=0; i<properties.size(); i++)
        ((Property)properties.get(i)).display();
    }
    
    if(locked != null)
      locked.postDisplay();
  }
  
  public void onMousePressed()
  {
    if(locked != null)
    {
      if(overRect(locked.getRect()))
      {
        locked.onMousePressed();
        return;
      }
      else
      {
        locked.lostFocus();
        locked = null;
      }
    }
    
    if(!overRect(_x, _y, _w, _h))
      return;
      
    if(scrollbar != null && overRect(scrollbar.getRect()))
      scrollbar.onMousePressed();
    
    for(int i=0; i<properties.size(); i++)
    {
      Property p = (Property)properties.get(i);
      if(overRect(p.getRect()))
      {
        p.onMousePressed();
        return;
      }
    }
  }
  
  public Rect getRect() { return new Rect(_x, _y, _w, _h); }
  
  public void onKeyPressed()
  {
    if(locked != null)
    {
      locked.onKeyPressed();
    }
  }
  
  public void addProperty(Property p)
  {
    properties.add(p);
    if(scrollbar != null)
      p.setPosition(_x, _y+_totalH, _w-15);
    else
      p.setPosition(_x, _y+_totalH, _w);
    _totalH += p.getHeight();
    
    if(_totalH > _h)
    {
      scrollbar = new VScrollbar(this, new Rect(_x+_w-15, _y, 14, _h-1));
      for(int i=0; i<properties.size(); i++)
      {
        Property tp = ((Property)properties.get(i));
        Rect r = tp.getRect();
        tp.setPosition(r._x, r._y, _w-15);
      }
    }
  }
  
  public void addButton(String name, ButtonCallback callback)
  { addProperty(new PropertyButton(this, name, callback)); }
  
  public void addDisplay(String name, MinyValue value)
  { addProperty(new PropertyDisplay(this, name, value)); }
  
  public void addEditBox(String name, MinyString value)
  { addProperty(new PropertyEditString(this, name, value)); }
  
  public void addEditBox(String name, MinyInteger value)
  { addProperty(new PropertyEditInteger(this, name, value)); }
  
  public void addEditBox(String name, MinyFloat value)
  { addProperty(new PropertyEditFloat(this, name, value)); }
  
  public void addSlider(String name, MinyInteger value, int mini, int maxi)
  { addProperty(new PropertySliderInteger(this, name, value, mini, maxi)); }
  
  public void addSlider(String name, MinyFloat value, float mini, float maxi)
  { addProperty(new PropertySliderFloat(this, name, value, mini, maxi)); }
  
  public void addCheckBox(String name, MinyBoolean value)
  { addProperty(new PropertyCheckBox(this, name, value)); }
  
  public void addList(String name, MinyInteger value, String choices)
  { addProperty(new PropertyList(this, name, value, choices)); }
}

interface ButtonCallback
{
  public void onButtonPressed();
}

interface MinyValue
{
  public String getString();
}

class MinyInteger implements MinyValue
{
  private Integer _v;
  MinyInteger(Integer v) { _v = v; }
  public Integer getValue() { return _v; }
  public void setValue(Integer v) { _v = v; }
  public String getString() { return _v.toString(); }
}

class MinyFloat implements MinyValue
{
  private Float _v;
  MinyFloat(Float v) { _v = v; }
  public Float getValue() { return _v; }
  public void setValue(Float v) { _v = v; }
  public String getString() { return _v.toString(); }
}

class MinyBoolean implements MinyValue
{
  private Boolean _v;
  MinyBoolean(Boolean v) { _v = v; }
  public Boolean getValue() { return _v; }
  public void setValue(Boolean v) { _v = v; }
  public String getString() { return _v.toString(); }
}

class MinyString implements MinyValue
{
  private String _v;
  MinyString(String v) { _v = v; }
  public String getValue() { return _v; }
  public void setValue(String v) { _v = v; }
  public String getString() { return _v; }
}

class Rect
{
  int _x, _y, _w, _h;
  Rect(int x, int y, int w, int h)
  { _x=x; _y=y; _w=w; _h=h; }
  
  public void grow(int v)
  { _x-=v; _y-=v; _w+=2*v; _h+=2*v; }
}

public boolean overRect(int x, int y, int width, int height) 
{
  if (mouseX >= x && mouseX <= x+width && 
      mouseY >= y && mouseY <= y+height) 
    return true;
  else
    return false;
}

public boolean overRect(Rect r) 
{ return overRect(r._x, r._y, r._w, r._h); }

public void rect(Rect r)
{ rect(r._x, r._y, r._w, r._h); }

public void text(String t, Rect r)
{ text(t, r._x, r._y, r._w, r._h); } 

interface MinyWidget
{
  public Rect getRect();
  public void update();
  public void display();
  public void lostFocus();
  public void onMousePressed();
  public void onKeyPressed();
  public void postDisplay();
}

class Property implements MinyWidget
{
  protected MinyGUI _parent;
  String _name;
  protected int _x, _y, _w;
  
  Property(MinyGUI parent, String name)
  {
    _parent = parent;
    _name = name;
  }
  
  public void setPosition(int x, int y, int w)
  {
    _x = x;
    _y = y;
    _w = w;
  }
  
  public int getHeight() { return 20; }
  public Rect getRect() { return new Rect(_x, _y, _w, getHeight()); }
  
  public void update() {}
  public void display()
  {
    textAlign(LEFT, CENTER);
    fill(_parent.fg);
    text(_name, _x+5, _y, _w * 0.4f - 7, 20); 
  }
  public void lostFocus() {}
  public void onMousePressed() {}
  public void onKeyPressed() {}
  public void postDisplay() {}
}

class PropertyButton extends Property
{
  ButtonCallback _callback;
  PropertyButton(MinyGUI parent, String name, ButtonCallback callback)
  { 
    super(parent, name);
    _callback = callback;
  }
  
  public Rect getBox()
  { return new Rect((int)(_x+_w*0.2f), _y+1, (int)(_w*0.6f), 18); }
  
  public void onMousePressed()
  {
    if(overRect(getBox()))
      _callback.onButtonPressed();
  }
  
  public void display()
  {
    stroke(_parent.fg); fill(_parent.bg);
    Rect b = getBox();
    if(!mousePressed && overRect(b) && _parent.hasLock(null))
      strokeWeight(2);
    rect(b);
    strokeWeight(1);
    b.grow(-1);
    textAlign(CENTER, TOP);
    fill(_parent.fg);
    text(_name, b);
  }
}

class PropertyDisplay extends Property
{
  MinyValue _value;
  PropertyDisplay(MinyGUI parent, String name, MinyValue value)
  {
    super(parent, name);
    _value = value;
  }
  
  public void display()
  {
    super.display();
    text(_value.getString(), _x + _w*0.4f + 3, _y, _w *0.6f - 8, 20);
  }
}

class PropertyEdit extends Property
{
  protected int cursorPos, cursorTime, selectionStart, selectionEnd;
  protected boolean cursorOn, selectioning;
  protected String editText;
  
  PropertyEdit(MinyGUI parent, String name)
  {
    super(parent, name);
    cursorPos = 0;
    cursorTime = 0;
    cursorOn = true;
    selectioning = false;
    selectionStart = selectionStart = -1;
  }
  
  public Rect getBox()
  { return new Rect((int)(_x+_w*0.4f+3), _y+1, (int)(_w*0.6f-8), 18); }
  
  public int findCursorPos()
  {
    float tc = mouseX - (int)(_x+_w*0.4f+4); 
    int closestPos = editText.length();
    float closestDist = _w;
    for(int i=editText.length(); i>=0; i--)
    {
      float tw = textWidth(editText.substring(0,i));
      float d = abs(tc-tw);
      if(d < closestDist)
      {
        closestDist = d;
        closestPos = i;
      }
      else
        break;
    }
    return closestPos;
  }
  
  public void onMousePressed()
  {
    if(overRect(getBox()))
    {
      if(!_parent.hasLock(this))
      {
        _parent.getLock(this);
        cursorTime = millis()+500;
        cursorOn = true;
        editText = getValue();
      }
           
      cursorPos = findCursorPos();
      selectioning = true;
      selectionStart = cursorPos;
    }
  }
  
  public void onKeyPressed()
  {
    switch(key)
    {
      case CODED:
        switch(keyCode)
        {
          case LEFT:
            cursorPos = max(cursorPos-1, 0);
            break;
          case RIGHT:
            cursorPos = min(cursorPos +1, editText.length());
            break;
        }
        break;
      case RETURN:
      case ENTER:
        lostFocus();
        break;
      case DELETE:
        if(!selectioning && (selectionStart != -1))
        {
          editText = editText.substring(0, selectionStart) + editText.substring(selectionEnd);
          cursorPos = selectionStart;
          selectionStart = selectionEnd = -1;
        }
        else if(cursorPos < editText.length())
          editText = editText.substring(0, cursorPos) + editText.substring(cursorPos+1);
        break;
      case BACKSPACE:
        if(!selectioning && (selectionStart != -1))
        {
          editText = editText.substring(0, selectionStart) + editText.substring(selectionEnd);
          cursorPos = selectionStart;
          selectionStart = selectionEnd = -1;
        }
        else if(cursorPos > 0)
        {
          editText = editText.substring(0, cursorPos-1) + editText.substring(cursorPos);
          cursorPos--;
        }
        break;
      default:
        if(!selectioning && (selectionStart != -1))
        {
          editText = editText.substring(0, selectionStart) + editText.substring(selectionEnd);
          cursorPos = selectionStart;
          selectionStart = selectionEnd = -1;
        }
        String tempText = editText.substring(0, cursorPos) + key + editText.substring(cursorPos);
        if(validate(tempText))
        {
          editText = tempText;
          cursorPos++;
        }
        break;
    }
  }
  
  public boolean validate(String test) { return true; }
  public void saveValue() {}
  public String getValue() { return ""; }
  
  public void lostFocus()
  {
    if(validate(editText))
      saveValue();
    cursorPos = 0;
    cursorOn = true;
    selectioning = false;
    cursor(ARROW);
    _parent.releaseLock(this);
  }
  
  public void update()
  {
    if(!_parent.hasLock(this))
      return;
      
    if(millis() > cursorTime)
    {
      cursorOn = !cursorOn;
      cursorTime = millis() + 500;
    }
    
    if(!mousePressed)
    {
      selectioning = false;
      if(selectionStart != selectionEnd)
      {
        int start = min(selectionStart, selectionEnd);
        int end = max(selectionStart, selectionEnd);
        selectionStart = start;
        selectionEnd = end;
      }
      else
        selectionStart = selectionEnd = -1;
    }
      
    if(overRect(getBox()))
      cursor(TEXT);
    else
      cursor(ARROW);
      
    if(selectioning)
      cursorPos = selectionEnd = findCursorPos();
  }
  
  public void display()
  {
    super.display();
    
    noFill(); stroke(_parent.fg);
    Rect b = getBox();
    if(_parent.hasLock(this))
      strokeWeight(2);
    rect(b);
    strokeWeight(1);
    b.grow(-1);
    textAlign(LEFT, CENTER);
    if(_parent.hasLock(this))
    {
      if(selectionStart != selectionEnd)
      {
        float tw1, tw2, tw;
        tw1 = textWidth(editText.substring(0, selectionStart));
        tw2 = textWidth(editText.substring(0, selectionEnd));
        tw = tw2-tw1;
        
        fill(_parent.selectColor); noStroke();
        rect(b._x+tw1, b._y+1, tw, b._h-2);
        noFill();
      }
      
      fill(_parent.fg);
      text(editText, b);
      if(cursorOn)
      {
        float tw = textWidth(editText.substring(0, cursorPos));
        line(_x+_w*0.4f+4+tw, _y +4, _x+_w*0.4f+4+tw, _y+17);
      }
    }
    else
      text(getValue(), b);
  }
}

class PropertyEditString extends PropertyEdit
{
  MinyString _value;
  
  PropertyEditString(MinyGUI parent, String name, MinyString value)
  {
    super(parent, name);
    _value = value;
  }
  
  public boolean validate(String test)
  { return textWidth(test) < getBox()._w-2; }
  
  public void saveValue()
  { _value.setValue(editText); }
  
  public String getValue()
  { return _value.getValue(); }
}

class PropertyEditInteger extends PropertyEdit
{
  MinyInteger _value;
  
  PropertyEditInteger(MinyGUI parent, String name, MinyInteger value)
  {
    super(parent, name);
    _value = value;
  }
  
  public boolean validate(String test)
  { 
    if(test.equals("-"))
      return true;
    try
    {
      Integer.parseInt(test);
      return true;
    }
    catch(NumberFormatException e)
    { return false; }
  }
  
  public void saveValue()
  { 
    try
    { _value.setValue(Integer.parseInt(editText)); }
    catch(NumberFormatException e) {} 
  }
  
  public String getValue()
  { return _value.getValue().toString(); }
}

class PropertyEditFloat extends PropertyEdit
{
  MinyFloat _value;
  
  PropertyEditFloat(MinyGUI parent, String name, MinyFloat value)
  {
    super(parent, name);
    _value = value;
  }
  
  public boolean validate(String test)
  { 
    if(test.equals("-"))
      return true;
    try
    {
      Float.parseFloat(test);
      return true;
    }
    catch(NumberFormatException e)
    { return false; }
  }
  
  public void saveValue()
  { 
    try
    { _value.setValue(Float.parseFloat(editText)); }
    catch(NumberFormatException e) {} 
  }
  
  public String getValue()
  { return _value.getValue().toString(); }
}

class PropertySlider extends Property
{
  protected boolean _over;
  
  PropertySlider(MinyGUI parent, String name)
  {
    super(parent, name);    
  }
  
  public float getPos() { return 0.0f; }
  public void setPos(float v) {}
  
  private Rect getBox()
  {
    float fpos = (_x + _w*0.4f + 8) + (_w*0.6f - 18) * getPos();
    return new Rect((int)fpos-5, _y+8, 10, 10);
  }
  
  public void update()
  {
    if(mousePressed) 
    {
      if(_over)
        _parent.getLock(this);
    }
    else
      _parent.releaseLock(this);
     
    if(_parent.hasLock(this))
    {
      float t = mouseX - (_x + _w*0.4f + 8);
      t /= _w*0.6f - 18;
      setPos(t);
    }
    else
      _over = overRect(getBox()) && !mousePressed;
  }
  
  public void onMousePressed()
  {
    if(overRect(getBox()))
    {
      _over = true;
      _parent.getLock(this);
    }
    else if(overRect((int)(_x + _w*0.4f + 3), _y+8, _x + _w-5, _y+15))
    {
      float t = mouseX - (_x + _w*0.4f + 8);
      t /= _w*0.6f - 18;
      setPos(t);
    }
  }
  
  public void display()
  {
    super.display();
    
    fill(_parent.bg); stroke(_parent.fg);
    line(_x + _w*0.4f + 3, _y+13, _x + _w-5, _y+13);
    if(_over || _parent.hasLock(this)) strokeWeight(2); 
    rect(getBox());
    strokeWeight(1);
  }
}

class PropertySliderInteger extends PropertySlider
{
  MinyInteger _value;
  int _mini, _maxi;
  
  PropertySliderInteger(MinyGUI parent, String name, MinyInteger value, int mini, int maxi)
  {
    super(parent, name);    
    _value = value;
    _mini = mini;
    _maxi = maxi;
  }
  
  public float getPos() 
  { return (_value.getValue() - _mini) / (float)(_maxi - _mini); }
  
  public void setPos(float v) 
  { _value.setValue(round(constrain(_mini+v*(_maxi-_mini), _mini, _maxi))); }
  
  public void update()
  {
    super.update();
    _value.setValue(constrain(_value.getValue(), _mini, _maxi));
  }
}

class PropertySliderFloat extends PropertySlider
{
  MinyFloat _value;
  float _mini, _maxi;
  
  PropertySliderFloat(MinyGUI parent, String name, MinyFloat value, float mini, float maxi)
  {
    super(parent, name);    
    _value = value;
    _mini = mini;
    _maxi = maxi;
  }
  
  public float getPos() 
  { return (_value.getValue() - _mini) / (_maxi - _mini); }
  
  public void setPos(float v) 
  { _value.setValue(constrain(_mini+v*(_maxi-_mini), _mini, _maxi)); }
  
  public void update()
  {
    super.update();
    _value.setValue(constrain(_value.getValue(), _mini, _maxi));
  }
}

class PropertyCheckBox extends Property
{
  MinyBoolean _value;
  
  PropertyCheckBox(MinyGUI parent, String name, MinyBoolean value)
  {
    super(parent, name);
    _value = value;
  }
  
  public Rect getBox()
  { return new Rect((int)(_x + _w*0.4f + 3), _y + 8, 10, 10); }
  
  public void display()
  {
    super.display();
    
    stroke(_parent.fg); fill(_parent.bg);
    Rect myBox = getBox();
    rect(myBox);
    
    if(_value.getValue())
    {
      fill(_parent.fg);
      myBox.grow(-2);
      rect(myBox);
    }
  }
  
  public void onMousePressed()
  {
    if(overRect(getBox()))
      _value.setValue(!_value.getValue());
  }
}

class VScrollbar implements MinyWidget
{
  MinyGUI _parent;
  float pos;
  protected Rect _zone;
  protected boolean _over;
  
  VScrollbar(MinyGUI parent, Rect zone)
  {
    _parent = parent;
    _zone = zone;
    pos = 0;
    _over = false;
  }
  
  public Rect getRect() { return _zone; }
  public Rect getBox()
  { return new Rect(_zone._x+2, (int)(_zone._y+2+pos*(_zone._h-24)), _zone._w-4, 20); }
  
  public void onMousePressed()
  {
    if(overRect(getBox()))
    {
      _over = true;
      _parent.getLock(this);
    }
    else
    {
      float t = mouseY - (_zone._y + 12);
      t /= _zone._h-24;
      pos = constrain(t, 0, 1);
    }
  }
  
  public void update()
  {
    if(mousePressed) 
    {
      if(_over)
        _parent.getLock(this);
    }
    else
      _parent.releaseLock(this);
      
    if(_parent.hasLock(this))
    {
      float t = mouseY - (_zone._y + 12);
      t /= _zone._h-24;
      pos = constrain(t, 0, 1);
    }
    else
      _over = overRect(getBox()) && !mousePressed;
  }
  
  public void display()
  {
    noFill(); stroke(_parent.fg);
    rect(_zone);
    
    fill(_parent.bg);
    if(_over || _parent.hasLock(this))
    {
      strokeWeight(2);
      Rect b = getBox();
      b._x++; b._w--; b._y++; b._h--;
      rect(b);
      strokeWeight(1);
    }
    else
      rect(getBox());
  }
  
  public void lostFocus() { _over = false; }
  public void onKeyPressed() {}
  public void postDisplay() {}
}

class PropertyList extends Property
{
  MinyInteger _value;
  String[] _choices;
  protected boolean _below, _moving, _over;
  protected int _selected;
  
  PropertyList(MinyGUI parent, String name, MinyInteger value, String choices)
  {
    super(parent, name);
    _value = value;
    _choices = split(choices, ';');
    _below = true;
    _selected = -1;
  }
  
  public Rect getBox()
  { return new Rect((int)(_x+_w*0.4f+3), _y+2, (int)(_w*0.6f-8), 18); }
  
  public Rect getRect()
  {
    if(!_parent.hasLock(this))
      return super.getRect();
    else
      return new Rect(_x, 2+(_below?_y:_y-(getHeight()-2)*_choices.length), _w, (getHeight()-2)*(_choices.length+1));
  }
  
  public void onMousePressed()
  {
    if(_parent.hasLock(this))
    {
       Rect b = getBox();
       int h = b._h;
       b.grow(-1);
       if(!_below) b._y -= (_choices.length+1) * h;
       for(int i=0; i<_choices.length; i++)
       {
         b._y+=h;
         if(overRect(b))
           _value.setValue(i);
       }
      _parent.releaseLock(this);
    }
    else if(overRect(getBox()))
    {
      _parent.getLock(this);
      Rect p = _parent.getRect();
      Rect b = getBox();
      if(b._y+b._h+(b._h-1)*_choices.length > p._y+p._h) _below = false;
      else _below = true;
      _selected = _value.getValue();
      _moving = true;
      _over = false;
    }
  }
  
  public void update()
  {
    _value.setValue(constrain(_value.getValue(), 0, _choices.length));
    
    if(_moving)
    {
      if(!mousePressed)
      {
        if(_over)
        {
          _value.setValue(_selected);
          _parent.releaseLock(this);
          return;
        }
        _moving = false;
      }
      
      _over = false;
      Rect b = getBox();
      int h = b._h;
      b.grow(-1);
      if(!_below) b._y -= (_choices.length+1) * h;
      for(int i=0; i<_choices.length; i++)
      {
        b._y+=h;
        if(overRect(b))
        {
          _selected = i;
          _over = true;
          break;
        }
      }
      
      if(!_over) _selected = _value.getValue();
    }
  }
  
  public void display()
  {
    super.display();
    noFill(); stroke(_parent.fg);
    Rect b = getBox();
    b._w-=14;
    rect(b);
    b.grow(-1);
    fill(_parent.fg);
    textAlign(LEFT, CENTER);
    text(_choices[(int)_value.getValue()], b);
    
    b = getBox();
    b._x += b._w-14;
    b._w = 14;
    noFill();
    rect(b);
    line(b._x+3, b._y+3, b._x+b._w/2, b._y+b._h-4);
    line(b._x+b._w/2, b._y+b._h-4, b._x+b._w-3, b._y+3);
  }
  
  public void postDisplay()
  {
    Rect b = getBox();
    b._y += _below ? b._h : -_choices.length*b._h;
    b._h *= _choices.length;
    fill(_parent.bg); stroke(_parent.fg);
    rect(b);
    
    b = getBox();
    int h = b._h;
    b.grow(-1);
    if(!_below) b._y -= (_choices.length+1) * h;
    noStroke(); fill(_parent.selectColor);
    rect(b._x, b._y+h*(1+_selected), b._w+1, b._h+1);
    fill(_parent.fg);
    textAlign(LEFT, CENTER);
    for(int i=0; i<_choices.length; i++)
    {
      b._y+=h;
      text(_choices[i], b);
    }
  }
}
 
  static public void main(String[] passedArgs) {
    String[] appletArgs = new String[] { "IHM" };
    if (passedArgs != null) {
      PApplet.main(concat(appletArgs, passedArgs));
    } else {
      PApplet.main(appletArgs);
    }
  }
}
