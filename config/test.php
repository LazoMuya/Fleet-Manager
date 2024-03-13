<!DOCTYPE html>
<html>
<head>
  <title>Clickable Element in HTML Textbox</title>
  <style>
    .container {
      position: relative;
      display: inline-block;
    }
    .icon {
      position: absolute;
      top: 10%;
      right: 5px;
      /*transform: translateY(-50%);*/
      cursor: pointer;
      /*pointer-events: auto;*/
    }
    .icon:hover{
      color: red;
    }
    .moving-word {
    display: inline-block;
    animation: wave 2.0s infinite;
    }

    @keyframes wave {
      0%, 100% {
        transform: translateY(0);
      }
      25%, 75% {
        transform: translateY(-5px);
      }
      50% {
        transform: translateY(5px);
      }
    }


  </style>
</head>
<body>
  <h1>Clickable Element in HTML Textbox Example</h1>
  <form onsubmit="testEmailAddress()">
  <div class="container">
    <input type="text" class="textbox" id="dateInput">
    <div class="icon" onclick="insertTodayDate()">Today</div>
  </div>
  <button class="submit">Submit</button>
  </form>
  HI.<p id="demo"></p><br>
  <p>Hover over the word <span class="moving-word">move</span> to see it in action!</p>

  <script>
    function insertTodayDate() {
	  var dateInput = document.getElementById("dateInput");
	  var today = new Date();
      var day = String(today.getDate()).padStart(2, '0');
      var month = String(today.getMonth() + 1).padStart(2, '0');
      var year = today.getFullYear();
      var formattedDate = day + '-' + month + '-' + year;

      if (dateInput.value === formattedDate)
        dateInput.value = "";
      else
        dateInput.value = formattedDate;
    }

    function testEmailAddress() {
      var dateInput = document.getElementById("dateInput").value;
      var at = dateInput.indexOf("@");
      document.getElementById("demo").value = (dateInput);
      if(at < 1){
        alert("Invalid Email");
        return false;
      }
      var dot = dateInput.indexOf(".");
      //document.write(dot);
      if(dot <= atSymbol + 2){
        alert("Invalid Email");
        return false;
      }
        
      if (dot === dateInput.length - 1){
        alert("Invalid email");
        return false;
      }

        

      return true;
    }
  </script>
</body>
</html>
