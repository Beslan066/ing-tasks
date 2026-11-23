import { toggleFiltersDropdown } from "../functions/main-admin-page/toggleFiltersDropdown";


document.querySelector('#filterToggle').addEventListener('click',()=> {
    console.log('tesc')
    toggleFiltersDropdown("filtersPanel","filterArrow")
})
