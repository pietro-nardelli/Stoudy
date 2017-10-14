<?php
function upload() {
    $target_dir = "uploads/"; //Questa è la directory nella quale caricheremo i PDF
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1; //E' una flag
    $fileType = pathinfo($target_file,PATHINFO_EXTENSION); 

    // Check if image file is PDF or fake PDF
    if (!empty ($_FILES["fileToUpload"]["tmp_name"])) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE); //Crea un fileinfo resource con il mime-type
        $mime = finfo_file($finfo, $_FILES['fileToUpload']['tmp_name']); //finfo_file ritorna l'informazione (in questo caso mime-type) del file che si vuole caricare
        if($mime != 'application/pdf') {
            return 0;
        }
    }

    // Controlliamo che il file abbia anche estensione.pdf (e non solo mime-type)
    if($fileType != "pdf" ) {
        return 0;
    }

    // Check file size (non più grande di 5MB)
    if ($_FILES["fileToUpload"]["size"] > 5000000) {
        return 0;
    }

    $time = time(); //Vogliamo un file con nome univoco
    $target_file = $target_dir . $time .".pdf"; //Rinominiamolo, quindi
    // Ma se esiste già... restituisci errore
    if (file_exists($target_file)) {
        return 0;
    }


    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        return $target_file; //Se tutto ok con il caricamento, allora restituisci il link al PDF
    }
    else { //Se ci sono problemi con il caricamento...
        return 0;
    }

}
?>