/* Outgoing message flags */
var flags = 0;

/**************************************************************************
FUNCTION setMessageFlags
---------------------------------------------------------------------------
Task:
  Set flags for outgoing message
---------------------------------------------------------------------------
Parameters:
  flagType        int           Flag type
                                (1: Bold, 2: Italic, 3: Underlined)
---------------------------------------------------------------------------
Return: --
**************************************************************************/
function setMessageFlags(flagType) {
  switch (flagType) {
    case 1: // Bold
      flags ^= 1;
      break;
    case 2: // Italic
      flags ^= 2;
      break;
    case 3: // Underlined
      flags ^= 3;
      break;
  }
  formatInput();
}

/**************************************************************************
FUNCTION formatInput
---------------------------------------------------------------------------
Task:
  Format message input frame depending on flags
---------------------------------------------------------------------------
Parameters: --
---------------------------------------------------------------------------
Return: --
**************************************************************************/
function formatInput() {
  try {
    var bold = "normal";
    var bold_button = "B";
    var italic = "normal";
    var italic_button = "I";
    var underlined = "none";
    var underlined_button = "U";
    if (flags & 1) {
      // Bold
      bold = "bold";
      bold_button = "B*";
    }
    if (flags & 2) {
      // Italic
      italic = "italic";
      italic_button = "I*";
    }
    if (flags & 3) {
      // Underline
      underlined = "underline";
      underlined_button = "U*";
    }
    if (typeof input.document.i.m.style.value == "string") {
      // Opera-type browser
      input.document.i.m.style = "font-weight: " + bold + "; font-style: " + italic + "; text-decoration: " + underlined + "; width: " + inputFieldWidth + "px;";
      input.document.i.bold.style = "font-weight: " + bold + "; width: 20px;";
      input.document.i.italic.style = "font-style: " + italic + "; width: 20px;";
      input.document.i.underlined.style = "text-decoration: " + underlined + "; width: 20px;";
    } else {
      // IE, Mozilla, Netscape ...
      input.document.i.m.style.fontWeight = bold;
      input.document.i.m.style.fontStyle = italic;
      input.document.i.m.style.textDecoration = underlined;
      input.document.i.m.style.width = inputFieldWidth;
      input.document.i.bold.style.fontWeight = bold;
      input.document.i.italic.style.fontStyle = italic;
      input.document.i.underlined.style.textDecoration = underlined;
    }
    input.document.i.bold.value = bold_button;
    input.document.i.italic.value = italic_button;
    input.document.i.underlined.value = underlined_button;
  } catch (e) {}
}
