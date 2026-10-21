// ==================== ПЕРЕКЛЮЧЕНИЕ ФИЛЬТРОВ ====================
export function toggleFiltersDropdown() {
    const dropdown = document.getElementById("filtersPanel");
    const chevron = document.getElementById("filterIcon");

    if (!dropdown || !chevron) return;
    const isHidden = dropdown.classList.contains("hidden");

    if (isHidden) {
        dropdown.classList.remove("hidden");
        dropdown.classList.remove("fade-out-x");
        dropdown.classList.add("fade-in-x");
        chevron.style.transform = "rotate(180deg)";
    } else {
        dropdown.classList.remove("fade-in-x");
        dropdown.classList.add("fade-out-x");

        chevron.style.transform = "rotate(0deg)";

        setTimeout(() => {
            if (dropdown.classList.contains("fade-out-x")) {
                dropdown.classList.add("hidden");
            }
        }, 200);
    }
}
