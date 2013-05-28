#include <AccelStepper.h>
#include <LiquidCrystal.h> 
#include <Keypad.h>

#define lenght 16
 
//MOTE
//AccelStepper stepperX(1, 9, 8);
//AccelStepper stepperY(1, 7, 6);
//AccelStepper stepperZ(1, 9, 8);
//int pos1 = 3600;
//int pos2 = 5678;
//int pos3 = 5678;

void analyseKey(String value);
boolean processingMode = false, debugMode = true;
int value, motorX = 0, motorY=0, motorZ=0;
int customDelay = 200, getvitesse=0, dejavu;
int percent=100;
String  processingValue, string;
String slicedMotor, slicedDirection, slicedPas, slicedSpeed;
unsigned char b;
const byte ROWS = 4; //four rows
const byte COLS = 4; //three columns
char keys[ROWS][COLS] = {
  {
    'F','E','D', 'C'  }
  ,
  {
    'B','A','9', '8'  }
  ,
  {
    '7','6','5', '4'  }
  ,
  {
    '3','2','1', '0'  }
};
byte rowPins[ROWS] = {5, 4, 3, 2};  
byte colPins[COLS] = {9, 8, 7, 6};  
byte ledPin = 13; 
boolean blink = false, aset = false;

LiquidCrystal lcd(12, 11, 5, 4, 3, 2);
Keypad keypad = Keypad( makeKeymap(keys), rowPins, colPins, ROWS, COLS );

void setup(){
  delay(100);
  lcd.begin(16, 2);  
  loading();
  
  /*stepperX.setMaxSpeed(3000);
  stepperX.setAcceleration(1000);
  stepperY.setMaxSpeed(2000);
  stepperY.setAcceleration(800);
  stepperZ.setMaxSpeed(3000);
  stepperZ.setAcceleration(1000);
  */
  
  Serial.begin(9600);
  pinMode(ledPin, OUTPUT);      
  digitalWrite(ledPin, HIGH);   
  keypad.addEventListener(keypadEvent); 
  lcd.setCursor(0,0);
  lcd.print("Liaison serie OK");
  delay(2000);
  lcd.clear();
  lcd.setCursor(0,2);
  lcd.blink();
}

void loop(){
 getMotorState();
  String content = "";
  char character;
  
  while(Serial.available()) {
    character = Serial.read();
    content.concat(character);
    delay(10);
  }

  if (content != "") {analysKey(content);}


  char key = keypad.getKey();

 
  if (blink){
    digitalWrite(ledPin,!digitalRead(ledPin));
    delay(100);
  }
}

void keypadEvent(KeypadEvent key){
  switch (keypad.getState()){
    
    
  case PRESSED:
    switch (key){
     
     case '7': 
     analysKey("x");     
     break;
     
     case '5': 
     analysKey("w");     
     break;
     
     case '6': 
     analysKey("s");     
     break;
     
     case 'F': 
     analysKey("q");     
     break;
     
     case 'A': 
     analysKey("t");     
     break;
     
     case '2': 
     analysKey("y");     
     break;
     
      case '0': 
     analysKey("a");     
     break;
     
      case '4': 
     analysKey("z");     
     break;
     
      case '1': 
     analysKey("1");     
     break;
      
    }
    break;
        
 /* case RELEASED:
    switch (key){
    case '2': 
  
      break;
    }
    break;
   */ 
   
  case HOLD:
    switch (key){
    case '2': 
      blink = true; 
      break;
    }
    break;
  }
}

void analysKey(String value)
{ 
      if (value == "w") {
      affiche("Moteur X (+) ON");
      motorX++;
      delay(customDelay);
    }
    else if (value == "x") {
      affiche("Moteur X (-) ON");
      motorX--;
      delay(customDelay);
    }
    else if (value == "a") {
      affiche("Moteur Z (+) ON");
      motorZ++;
      delay(customDelay);
    }
    else if (value == "z") {
      affiche("Moteur Z (-) ON");
      motorZ--;
      delay(customDelay);
    }
    else if (value == "s") {
      lcd.noBlink();
      while(1)
      {
        motorX=0;motorY=0;motorZ=0;
      delay(800);
      digitalWrite(ledPin,!digitalRead(ledPin));
      affiche(" Arret d'ugence ");
      delay(800);
      digitalWrite(ledPin,!digitalRead(ledPin));
      lcd.clear();
      }
    }
    else if (value == "t") {
      affiche("Moteur Y (+) ON");
      motorY++;
      delay(customDelay);
    }
    else if (value == "y") {
      affiche("Moteur Y (-) ON");
      motorY--;
      delay(customDelay);
    }
    else if (value == "q") {
      affiche("Liais. serie OFF");
      motorX=0;motorY=0;motorZ=0;
      delay(500);
      exit(0);
    }
    else if (value == "o") {
      affiche("Retour origine ");
      motorX=0;motorY=0;motorZ=0;
      delay(customDelay);
    }
     else if (value == "processing" || processingMode == true) {

       if(!processingMode){processingMode = true;}
       
       if(value != "" && value != "processing"){processingValue = value;} // && ici ?

        if(processingValue != "" && processingValue != "processing")
       {  

        // affiche("                ");
         affiche(processingValue);
                  
         // PARTIE TRAITEMENT DE L'INFO ET ENVOIE MOTEUR 
 
 
        /* slicedMotor = string.substring(0,1);
          Serial.println("Moteur : "+slicedMotor);

          slicedDirection = string.substring(1,2);
            Serial.println("Direction : "+slicedDirection);

          slicedPas = string.substring(2, string.indexOf("v"));
            Serial.println("Nb. Pas : "+slicedPas);

          slicedSpeed = string.substring(string.indexOf("v")+1,string.length());
             Serial.println("Vitesse : "+slicedSpeed);
*/
 
                       
         delay(800);
         
           
           processingValue = "";
           processingMode = false;
           
           lcd.clear();
           value = "";

       }
    }
    else {
      affiche(" Comnd Inconnue ");
      delay(customDelay);
    } 
    
   
}

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
  lcd.print(" V1.2 Bienvenue");
   delay(2500);
  lcd.clear();
  
  while(1)
  {
  lcd.setCursor(0, 0); 
  x+=random(0,2);
  percent = x;
  lcd.print("Chargement ");
  lcd.print(percent);
  lcd.print("%   ");
  lcd.setCursor(0,1);
 
  if(x == 100){delay(1300);break;}
  delay(40);
  y++;
} 
  }
}

void getMotorState(void)
{
  if(processingMode == false)
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
else
{
  lcd.setCursor(0,0);
  lcd.print("Mode Processing!");
}
}

/*
** FONCTION sendToFactory : Traitement de processingValue (Découpage/Extraction/Conversion/Traitement)
** @Params: String
** @Output: ArduinoSide
*/

boolean sendToFactory(String val)
{
  affiche(val);
  return true;
}

/*
** FONCTION affiche : Affiche sur ligne 2
** @Params: String
** @Output: Void
*/
void affiche(String str)
{
  lcd.setCursor(0,2);
  lcd.print(str);
}


