export function updateViewModeBeforeSubmit() {
    const mode = document
        .querySelector("#viewModeListBtn")
        .classList.contains("bg-green-600")
        ? "list"
        : "kanban";
    const filterForm = document.getElementById("filterForm");
    const filterFormList = document.getElementById("filterFormList");

    const formToUse = filterForm || filterFormList;
    if (formToUse) {
        let viewModeInput = formToUse.querySelector('input[name="view_mode"]');
        if (!viewModeInput) {
            viewModeInput = document.createElement("input");
            viewModeInput.type = "hidden";
            viewModeInput.name = "view_mode";
            formToUse.appendChild(viewModeInput);
        }
        viewModeInput.value = mode;
        formToUse.submit();
    }
}
export function resetWithCurrentMode() {
    const mode = document
        .querySelector("#viewModeListBtn")
        .classList.contains("bg-green-600")
        ? "list"
        : "kanban";
    const url = new URL(window.location.href);
    // Очищаем все параметры фильтрации
    url.searchParams.delete("search");
    url.searchParams.delete("status");
    url.searchParams.delete("user_id");
    url.searchParams.delete("department_id");
    url.searchParams.delete("priority");
    url.searchParams.delete("category_id");
    url.searchParams.delete("sort");
    url.searchParams.delete("order");
    url.searchParams.set("view_mode", mode);
    window.location.href = url.toString();
}
