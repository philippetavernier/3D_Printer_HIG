#include <AccelStepper.h>
#include <LiquidCrystal.h> 
#include <AFMotor.h>
#include <Keypad.h>

#define lenght 16
 
void analyseKey(String value);
boolean processingMode = false, debugMode = true;
int value, motorX = 0, motorY=0, motorZ=0;
int customDelay = 200, getvitesse=0, dejavu;
int percent=100;
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

AF_Stepper motorx(100, 2);
AF_Stepper motory(100, 1);

void setup(){
  delay(100);
  lcd.begin(16, 2);  
  loading();
 
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
    else if (value.substring(0,1) == "m") {
      
      String moteur = value.substring(1,2);
      String dir = value.substring(5,6);
      String pas = value.substring(6,value.indexOf("v"));
      String vitesse = value.substring(value.indexOf("v")+1,value.length());
      
      int _v = vitesse.toInt();
      int _d = dir.toInt();
      int _p = pas.toInt();
      
      lcd.setCursor(0,2);
      lcd.print("Action moteur ! ");
      delay(500);
      
      if(moteur == "x")
{
  motorx.setSpeed(_v); 
if(_d == 1)
{
    motorx.step(_p, FORWARD, SINGLE); 
    Serial.println("AVANT");
}
else
{
    motorx.step(_p, BACKWARD, SINGLE); 
}
}
else if(moteur == "y")
{
  motory.setSpeed(_v); 
  
 if(_d == 1)
{
    motory.step(_p, FORWARD, SINGLE); 
}
else
{
    motory.step(_p, BACKWARD, SINGLE); 
}
}


       delay(customDelay);
    
    
    }
 
    else {
      affiche(" Comnd Inconnue ");
      delay(customDelay);
    } 
    
   lcd.clear();
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
** @Params: String
** @Output: Void
*/
void affiche(String str)
{
  lcd.setCursor(0,2);
  lcd.print(str);
}

 
