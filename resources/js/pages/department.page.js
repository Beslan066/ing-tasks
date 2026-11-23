import { toggleFiltersDropdown } from "../functions/main-admin-page/toggleFiltersDropdown";


document.querySelector('#filterToggle').addEventListener('click',()=> {
    toggleFiltersDropdown("filtersPanel","filterIcon")
})
