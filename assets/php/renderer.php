<?php
  // function to render page templates quickly and effectively
  // was seperated to simplify and to permit global page vars.
  function render_page($templateName, $pageVarArray)
  {
    Twig_Autoloader::register();

    $pathToTemplates = $GLOBALS["basePath"] . "/assets/templates";

    if (isset($pageVarArray)) {
      $pageVars = array_merge($GLOBALS["rendererVars"], $pageVarArray);
    } else {
      $pageVars = $GLOBALS["rendererVars"];
    }

    try {
      // load twig
      $loader = new Twig_Loader_Filesystem($pathToTemplates);
      $twig = new Twig_Environment($loader);
      $template = $twig->loadTemplate($templateName);

      // render template
      echo $template->render($pageVars);
    } catch (Exception $e) {
      die (logEvent("Error with Twig: " . $e->getMessage(), "page_renderer"));
    }
  }
