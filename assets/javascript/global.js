/* Created by Thomas 'Purger' Jones (tjone270) on 30/06/2016 */
/* Global JavaScript scripting file */


function toggleWeaponCheckboxes() {
  var element = document.getElementsByClassName("weapon_checkbox");
  for (var i = 0; i < element.length; i++) {
    if (element[i].disabled) {
      element[i].disabled = false;
    } else {
      element[i].disabled = true;
    }
  }
}

function toggleInstagib() {
  var element = document.getElementsByClassName("infiniteammo-radio");
  var weapon_checkboxes = document.getElementsByClassName("weapon_checkbox");
  for (var i = 0; i < element.length; i++) {
    if (element[i].disabled) {
      element[i].disabled = false;
      document.getElementById("defaultWeaponsCheckbox").disabled = false;
      if (!document.getElementById("defaultWeaponsCheckbox").checked) {
        for (var j = 0; j < weapon_checkboxes.length; j++) {
          weapon_checkboxes[j].disabled = false;
        }
      } else {
        for (var j = 0; j < weapon_checkboxes.length; j++) {
          weapon_checkboxes[j].disabled = true;
        }
      }
      document.getElementById("instaGibMessage").innerHTML = "";
    } else {
      element[i].disabled = true;
      document.getElementById("defaultWeaponsCheckbox").disabled = true;
      for (var k = 0; k < weapon_checkboxes.length; k++) {
        weapon_checkboxes[k].disabled = true;
      }
      document.getElementById("instaGibMessage").innerHTML = "Weapon selection has been overridden to Gauntlet and Railgun while Insta-Gib is enabled.<br />Infinite ammo has been overridden to always on while Insta-Gib is enabled.";
    }
  }
}

function updateFactory() {
  factory_name = document.getElementById("server_factory");
  factory_name = factory_name.options[factory_name.selectedIndex].text;
  checkboxHtml = "<input type=\"checkbox\" id=\"defaultWeaponsCheckbox\" onclick=\"toggleWeaponCheckboxes()\" name=\"g_startingWeapons[]\" value=\"DEFAULT\" checked>" // this is _REALLY_ shitty...
  document.getElementById("defaultWeaponsCheckboxLabel").innerHTML = checkboxHtml + "Default for " + factory_name;
}

function showMessageBox() {
  $(document).ready(function () {
    $('#messageModal').modal('show');
  });
}
