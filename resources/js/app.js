import "./bootstrap";

import Alpine from "alpinejs";
import { initFilePreview } from "./components/file-preview";
import { addTaskToColumn } from "./functions/add-task-to-column";
import { showNotification } from "./functions/show-notification";
import { getCsrfToken } from "./helpers/getCsrfToken";
window.Alpine = Alpine;
window.addTaskToCol = addTaskToColumn;
window.showNotification = showNotification;
window.getCsrfToken = getCsrfToken;
Alpine.start();
document.addEventListener("DOMContentLoaded", () => {
    initFilePreview();

    console.log("initFilePreview");
});
//  console.log('toke:',window.getCsrfToken())
function downloadTaskFile(fileId) {
    console.log("downloadTaskFile");
    window.open(`/file-storage/download/${fileId}`, "_blank");
}

window.downloadTaskFile = downloadTaskFile;
