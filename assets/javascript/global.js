/* Created by Thomas 'Purger' Jones (tjone270) on 30/06/2016 */
/* Global JavaScript scripting file */


function enableWeaponCheckboxes() {
  var checkboxes = document.getElementsByClassName("weapon_checkbox");
  for (var i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i].disabled) {
      checkboxes[i].disabled = false;
    } else {
      checkboxes[i].disabled = true;
    }
  }
}

function updateFactory() {
  factory_name = document.getElementById("server_factory");
  factory_name = factory_name.options[factory_name.selectedIndex].text;
  checkboxHtml = "<input type=\"checkbox\" onclick=\"enableWeaponCheckboxes()\" name=\"g_startingWeapons[]\" value=\"DEFAULT\" checked>" // this is _REALLY_ shitty...
  document.getElementById("defaultWeaponsCheckboxLabel").innerHTML = checkboxHtml + "Default for " + factory_name;
}

function showMessageBox() {
  $(document).ready(function () {
    $('#messageModal').modal('show');
  });
}
