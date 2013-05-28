/*   ___     ___     ___     ___     ___     ___     ___     ___
 ___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/   \___
/   \___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/   \
\___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/
/   \___/                                                   \___/   \
\___/       Paul FASOLA                           2012-2013     \___/
/   \                    Interface Homme-Machine                /   \
\___/                                                           \___/
/   \___                  Code Principal                     ___/   \
\___/   \___     ___     ___     ___     ___     ___v 1.9___/   \___/
/   \___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/   \
\___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/
    \___/   \___/   \___/   \___/   \___/   \___/   \___/   \___/
*/

#include <LiquidCrystal.h> 
#include <AccelStepper.h>
#include <Keypad.h>

/*
** Déclaration des constantes
*/

#define lenght 16
#define __thiscall __cdecl
#define lenght 16

/*
** Options du programme
*/

boolean holdNegativeX = false, holdPositiveX = false;   
boolean holdNegativeY = false, holdPositiveY = false;    
boolean holdNegativeZ = false, holdPositiveZ = false;   
boolean holdPositiveMul = false, holdNegativeMul = false;
boolean processingMode = false;  //force le mode processing
boolean debugMode = false;       //mode debuggage
boolean blink = false;           //témoin "occupé"
boolean aset = false;            //Asetter.

/******/

void analysKey(String value, boolean isKeypad = false); // Prototype de analysKey

int value, motorX = 0, motorY=0, motorZ=0;
int customDelay = 300, dejavu, percent=100, multiplier = 1;

const byte ROWS = 4; //Lignes du tableau (Keypad)
const byte COLS = 4; //Colonnes du tableau (Keypad)

byte rowPins[ROWS] = {5, 4, 3, 2};  
byte colPins[COLS] = {9, 8, 7, 6};  
byte ledPin = 13; 

char keys[ROWS][COLS] = {
  {
    'F','E','D', 'C'  },
  {
    'B','A','9', '8'  },
  {
    '7','6','5', '4'  },
  {
    '3','2','1', '0'  }
};

String  processingValue, string;
unsigned char b;

/*
** Initialisation du LCD, des moteurs et du clavier (Déclaration des pins)
*/

Keypad keypad = Keypad( makeKeymap(keys), rowPins, colPins, ROWS, COLS ); 
LiquidCrystal lcd(12, 11, 34, 30, 26, 22);

AccelStepper motx(AccelStepper::FULL2WIRE, 44, 46); 
AccelStepper moty(AccelStepper::FULL2WIRE, 47, 49);
AccelStepper motz(AccelStepper::FULL2WIRE, 38, 40);

 
/*
** Initialisation du programme
*/
int pos = 3600;
void setup()
{
  delay(100);
  lcd.begin(16, 2);  
 
  loading(); // Chargeur graphique + init variable
 
  Serial.begin(9600); // Initialisation de la connexion série

  keypad.addEventListener(keypadEvent); // Écoute du clavier (events)
  lcd.setCursor(0,0);
  lcd.print("Liaison serie OK");
  delay(2000);
  digitalWrite(45, HIGH);
  lcd.clear();
  lcd.setCursor(0,2);
  lcd.blink();

    motx.setAcceleration(3000);
        motx.setMaxSpeed(1000);
    moty.setAcceleration(1000);
        moty.setMaxSpeed(3000);
    motz.setAcceleration(1000);
      motz.setMaxSpeed(3000);
}


void loop()
{  
  
 getMotorState(); // Demande le tableau contenant la "position" de chaque moteurs
 
 /*
 ** Partie analyse de la réception de données arrivant par le port série
 */
 
 
 String content = "";
 char character;
  
  while(Serial.available()) 
  {
    character = Serial.read(); // [sp-4h] [bp-88h]@1
    content.concat(character); 
    delay(10);
  }

  if (content != "") {analysKey(content);} // Si on détecte le moindre caractère -> On analyse sa forme via la fonction AnalysKey.
  
  char key = keypad.getKey(); // Si une touche de clavier est activée, on fait un callback de keypadEvent avec pour paramètre la touche (fonction getKey())

  //Boucles pour le case HOLD
  if (holdNegativeX)
  {
    motorX -= 1 * multiplier;
    delay(300);
  }
  else if (holdPositiveX)
  {
    motorX += 1 * multiplier;
    delay(300);
  }
  else if (holdNegativeY)
  {
    motorY -= 1 * multiplier;
    delay(300);
  }
  else if (holdPositiveY)
  {
    motorY += 1 * multiplier;
    delay(300);
  }  
  else if (holdNegativeZ)
  {
    motorZ -= 1 * multiplier;
    delay(300);
  }
  else if (holdPositiveZ)
  {
    motorZ += 1 * multiplier;
    delay(300);
  }    
}

/*
** Fonction keypadEvent : Traite les évenements du clavier en fonction de la touche pressée
** Type: CallBack func
** @params : @callback{keypad.getKey()}
** @output : hybrid
*/

void keypadEvent(KeypadEvent key){
  switch (keypad.getState())
  {
  case PRESSED:
    switch (key)
    {
     case '7': 
       analysKey("x", true);  // Renvoie chaque touche à la fonction d'anlyse principale : analysKey
     break;
     
     case '5': 
       analysKey("w", true);     
     break;
     
     case '6': 
       analysKey("s");     
     break;
     
     case 'F': 
       analysKey("q");     
     break;
     
     case 'A': 
       analysKey("y", true);     
     break;
     
     case '2': 
       analysKey("t", true);     
     break;
     
     case '0': 
       analysKey("a", true);     
     break;
     
     case '4': 
       analysKey("z", true);     
     break;
     
     case '3': 
       analysKey("so");     
     break;
     
     case 'D': 
       multiplier -= 25;
         if(multiplier < 1)
         {
           multiplier = 1;
           lcd.setCursor(0,2);
           lcd.print("Err: Facteur < 0");    
             delay(customDelay);
           lcd.clear();
         }
         else
         {
           lcd.setCursor(0,2);
           lcd.print("Facteur : " + String(multiplier));    
             delay(customDelay);
           lcd.clear();
         }
     break;
     
     case 'C': 
       if(multiplier ==1)
       {
         multiplier += 24;
       }
       if(multiplier < 800)
       {
         multiplier += 25; 
       }
         lcd.setCursor(0,2);
         lcd.print("Facteur : " + String(multiplier));       
           delay(customDelay);  
         lcd.clear();
     break;
    }
    break;
        
 case RELEASED:
    switch (key)
    {
     case '7': 
       holdNegativeX = false;
     break;
     
     case '5': 
       holdPositiveX = false; 
     break;
 
     case 'A': 
       holdNegativeY = false;   
     break;
     
     case '2': 
       holdPositiveY = false;    
     break;
     
     case '4': 
       holdNegativeZ = false;   
     break;
     
     case '0': 
       holdPositiveZ = false;    
     break;      
    }
     break;
 
  case HOLD:   // Si la touche en question est maintenue
    switch (key)
    {
     case '7': 
       holdNegativeX = true;
     break;
     
     case '5': 
       holdPositiveX = true; 
     break;
 
     case 'A': 
       holdNegativeY = true;   
     break;
     
     case '2': 
       holdPositiveY = true;    
     break;
     
     case '4': 
       holdNegativeZ = true;   
     break;
     
     case '0': 
       holdPositiveZ = true;    
     break;
     
      case 'C': 
       holdPositiveMul = true;    
     break; 
    }
    break;
  }
}

/*
** Fonction analysKey : Fonction d'analyse principale. Analyse la valeur en paramètre et agit en fonction.
** @params : string, Boolean (Predefined if not)
** @output : Multiple{string, bool, int, hybrid}
*/

void analysKey(String value, boolean isKeypad)
{ 
      if (value == "w") 
      {
        affiche("Moteur X (+) ON");
        if(isKeypad){motorX += 1 * multiplier;}
                else{motorX++;}
          motx.run();
          motx.runToNewPosition(motx.currentPosition() + multiplier);
          motx.stop();
                     
      delay(customDelay);
    }
    else if (value == "x") 
    {
      affiche("Moteur X (-) ON");
      if(isKeypad){motorX -= 1 * multiplier;}
              else{motorX--;}
          motx.run();
          motx.runToNewPosition(motx.currentPosition() - multiplier);
          motx.stop();
      delay(customDelay);
    }
    else if (value == "a") 
    {
      affiche("Moteur Z (+) ON");
      if(isKeypad){motorZ += 1 * multiplier;}
              else{motorZ++;}
          motz.run();
          motz.runToNewPosition(motz.currentPosition() + multiplier);
          motz.stop();
      delay(customDelay);
    }
    else if (value == "z") 
    {
          motz.run();
          motz.runToNewPosition(motz.currentPosition() - multiplier);
          motz.stop();
      affiche("Moteur Z (-) ON");
      if(isKeypad){motorZ -= 1 * multiplier;}
              else{motorZ--;}
      delay(customDelay);
    }
    else if (value == "s")  // Arrêt d'urgence -> Passe par un "canal" prioritaire (tourne en fond, en mode asynchrone), peut être appelé a tout moment (même en pleine tâche, ce qui est le but.)
    {
      lcd.noBlink();
      while(1)
      {
        motorX=0;motorY=0;motorZ=0;
          delay(800);
        digitalWrite(44,!digitalRead(ledPin));
         lcd.setCursor(0,0);
         lcd.print("------> ! <-----");
         affiche(" Arret d'ugence ");
          delay(800);
        digitalWrite(44,!digitalRead(ledPin));
        lcd.clear();
      }
    }
    else if (value == "t") 
    {
      affiche("Moteur Y (+) ON");
      if(isKeypad){motorY += 1 * multiplier;}
              else{motorY++;}
      moty.run();
      moty.runToNewPosition(moty.currentPosition() + multiplier);
      moty.stop();
        delay(customDelay);
    }
    else if (value == "y") 
    {
      affiche("Moteur Y (-) ON");
      if(isKeypad){motorY -= 1 * multiplier;}
              else{motorY--;}
          moty.run();
          moty.runToNewPosition(moty.currentPosition() - multiplier);
          moty.stop();
      delay(customDelay);
    }
    else if (value == "q") 
    {
      affiche("Liais. serie OFF");
      motorX=0;motorY=0;motorZ=0;
        delay(500);
      exit(0);
    }
    else if (value == "o") 
    {
      affiche("Retour origine ");
      motorX=0;motorY=0;motorZ=0;
        delay(customDelay);
    }
       else if (value == "so") 
    {
      affiche(" * Etalonnage * ");
      motorX=0;motorY=0;motorZ=0;
        delay(customDelay);
    }
    else if (value.substring(0,1) == "m") 
    {
      sendToFactory(value); // On détecte une instruction de type mot {MOT}{DIR}{PAS} -> On l'envoie dans une fonction de traitement.
    }
    else if (value.substring(0,3) == "end") 
    {
      affiche("Fin du programme");
        delay(customDelay+1000);
    }
    else 
    {
      lcd.setCursor(0,0);
      lcd.print(value);
      affiche(" Comnd Inconnue "); // Valeur par défaut.
        delay(customDelay);
    } 
    
   lcd.clear();
}

/*
** Fonction loading : Fonction de chargement & sert de welcome screen.
** @params : void
** @output : void #[Precaching]
*/

void loading(void)
{   
  if(!debugMode)
  {
    int x = 0;
    int y = 0;
  
    lcd.setCursor(0, 0);
    lcd.print("Paul Fasola");
    lcd.setCursor(0, 2);
    lcd.print("     presente...");
    lcd.setCursor(0, 0);
      delay(2500);
    lcd.print(" IHM PROJET BAC ");
    lcd.setCursor(0, 2);
    lcd.print(" V1.9 Bienvenue");
      delay(2500);
    lcd.clear();
  
  while(1)
  {
    lcd.setCursor(0, 0); 
      x+=random(0,2);
      percent = x;
    lcd.print(" Chargement ");
    lcd.print(percent);
    lcd.print("%   ");
    lcd.setCursor(0,1);
 
    if(x == 100){delay(1300);break;}
      delay(40);
    y++;
} 
  }
}

/*
** Fonction getMotorState : Tableau qui retourne en première ligne du LCD la position de chaque moteur.
** @params : void
** @output : hybrid
*/

void getMotorState(void)
{
  lcd.setCursor(0,0);
  lcd.print("X:");
  lcd.print(motorX);
  lcd.print(" Y:");
  lcd.print(motorY);
  lcd.print(" Z:");
  lcd.print(motorZ);
  lcd.print("        ");
}

/*
** FONCTION affiche : Affiche sur ligne 2
** @params: String
** @Output: Void
*/

void affiche(String str)
{
  lcd.setCursor(0,2);
  lcd.print(str);
    delay(customDelay + 200);
}

/*
** FONCTION sendToFactory : Traitement de l'instruction moteur reçue (Découpage/Extraction/Conversion/Traitement)
** @params: String
** @output: @{VARS, PIN}
*/
 
 void sendToFactory(String val)
{
  delay(150);
  
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("Commande Moteur");
  
String slicedMotor = val.substring(4,5);
String slicedDirection = val.substring(5,6);
String slicedPas = val.substring(6,val.length());

int pas = slicedPas.toInt();
int dir = slicedDirection.toInt();

/* DEBUG::OUTPUT
Serial.println("****************** Commande moteur recue ******************");
Serial.println("** Moteur: "+ slicedMotor);
Serial.println("** Direction: "+ slicedDirection);
Serial.println("** Pas: "+ slicedPas);
Serial.println("***********************************************************");
*/

lcd.setCursor(0,2);
lcd.print(val);

 
if(slicedMotor == "x")
{

if(dir == 1)
{

  motx.run();
  motx.runToNewPosition(motx.currentPosition() + pas);
  motx.stop();
        motorX += pas;
    delay(customDelay + 200);
}
else
{
 
  motx.run();
  motx.runToNewPosition(motx.currentPosition() - pas);
  motx.stop();

    motorX -= pas;
    delay(customDelay + 200);
}
}
else if(slicedMotor == "y")
{
  
 if(dir == 1)
{
  moty.run();
  moty.runToNewPosition(moty.currentPosition() + pas);
  moty.stop();
  
    motorY += pas;
    delay(customDelay + 200);
}
else
{ 
    moty.run();
    moty.runToNewPosition(moty.currentPosition() - pas);
    moty.stop();
  
    motorY -= pas;
    delay(customDelay + 200);
}
}
else if(slicedMotor == "z")
{
 if(dir == 1)
{
  motz.run();
  motz.runToNewPosition(motz.currentPosition() + pas);
  motz.stop();
  
  motorZ += pas;
  delay(customDelay + 200);
}
else
{ 
  motz.run();
  motz.runToNewPosition(motz.currentPosition() - pas);
  motz.stop();
    motorZ -= pas;
    delay(customDelay + 200);
}
}
delay(700);
}
// PVOID __stdcall ExAllocatePool(POOL_TYPE PoolType, SIZE_T NumberOfBytes);
// NTSTATUS __fastcall IofCallDriver(PDEVICE_OBJECT DeviceObject, PIRP Irp);

