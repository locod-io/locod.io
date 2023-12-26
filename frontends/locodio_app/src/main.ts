/*
 * This file is part of the Locod.io software.
 *
 * (c) Koen Caerels
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {createApp} from 'vue'
import {createPinia} from 'pinia'
import App from './App.vue'
import router from './router'
import PrimeVue from 'primevue/config';

// ------------------------------------------------------------------------------------------------ prime vue components
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Divider from "primevue/divider";
import TabMenu from 'primevue/tabmenu';
import Menu from 'primevue/menu';
import Sidebar from 'primevue/sidebar';
import Splitter from 'primevue/splitter';
import SplitterPanel from 'primevue/splitterpanel';
import ConfirmPopup from "primevue/confirmpopup";
import RadioButton from 'primevue/radiobutton';
import Dropdown from 'primevue/dropdown';
import Textarea from 'primevue/textarea';
import OverlayPanel from 'primevue/overlaypanel';
import Badge from 'primevue/badge';
import Fieldset from 'primevue/fieldset';
import InputSwitch from 'primevue/inputswitch';
import Checkbox from 'primevue/checkbox';
import Toast from 'primevue/toast';
import ProgressSpinner from 'primevue/progressspinner';
import Skeleton from 'primevue/skeleton';
import ToggleButton from 'primevue/togglebutton';
import SelectButton from 'primevue/selectbutton';
import Tooltip from 'primevue/tooltip';
import ColorPicker from 'primevue/colorpicker';
import Dialog from 'primevue/dialog';
import Chips from 'primevue/chips';
import DataView from 'primevue/dataview';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import Image from 'primevue/image';
import MultiSelect from 'primevue/multiselect';
import AutoComplete from 'primevue/autocomplete';
import BadgeDirective from "primevue/badgedirective";
import Slider from 'primevue/slider';

// ------------------------------------------------------------------------------------------------------------ services
import ToastService from "primevue/toastservice";
import ConfirmationService from "primevue/confirmationservice";

// --------------------------------------------------------------------------------------------------------- stylesheets
import './assets/tailwind.css';
import './assets/application.css';
import './assets/documentation.css';
import './assets/editor.css';
import 'simple-syntax-highlighter/dist/sshpre.css';
import 'primevue/resources/primevue.min.css';
import 'primeicons/primeicons.css';
import 'dropzone/dist/dropzone.css';
import './assets/TableOperationsSolid.css';
import './assets/ProjectStatus.css';

// import 'primevue/resources/themes/soho-dark/theme.css';
// ------------------------------------------------------------------------------------------------------------ vue flow
/* these are necessary styles for vue flow */
import '@vue-flow/core/dist/style.css';
/* this contains the default theme, these are optional styles */
import '@vue-flow/core/dist/theme-default.css';

// --------------------------------------------------------------------------------------------------------------- axios
import axios from "axios";
// ---------------------------------------------------------------------------------------------------- vue progress bar
// @ts-ignore
import VueProgressBar from '@aacassandra/vue3-progressbar';
// -------------------------------------------------------------------------------------------------- font awesome icons
import {library} from '@fortawesome/fontawesome-svg-core'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import {
  faAlignCenter,
  faAlignLeft,
  faAlignRight,
  faArrowAltCircleDown as faArrowAltCircleDownSolid,
  faArrowAltCircleRight as faArrowAltCircleRightSolid,
  faArrowUpRightFromSquare,
  faBold,
  faBoltLightning,
  faCheck,
  faChevronCircleDown,
  faChevronDown,
  faChevronLeft,
  faChevronRight,
  faCircleChevronLeft,
  faCircleChevronRight,
  faCircleMinus,
  faCircleNotch,
  faCirclePlus,
  faCloudArrowDown,
  faCode,
  faCodeFork,
  faDiagramProject,
  faFileArrowUp,
  faFileExport,
  faFileImport,
  faFilePdf,
  faGlasses,
  faHighlighter,
  faHome,
  faItalic,
  faLink,
  faLinkSlash,
  faListOl,
  faListUl,
  faMoon,
  faParagraph,
  faPeopleGroup,
  faQuoteRight,
  faRectangleXmark,
  faSquare,
  faStrikethrough,
  faSun,
  faTableCells,
  faTableCellsLarge,
  faTerminal,
  faTextSlash,
  faTimeline,
  faToggleOff,
  faToggleOn,
  faUser,
  faUserGear,
  faUserSecret,
  faUserXmark,
} from '@fortawesome/free-solid-svg-icons';
import {faRobot} from "@fortawesome/free-solid-svg-icons/faRobot";
import {faMarkdown} from "@fortawesome/free-brands-svg-icons";

import {faArrowAltCircleDown, faArrowAltCircleRight,} from "@fortawesome/free-regular-svg-icons";
import {VueShowdownPlugin} from 'vue-showdown';
import ganttastic from '@infectoone/vue-ganttastic'

axios.defaults.baseURL = import.meta.env.VITE_API_URL as string;

// --------------------------------------------------------------------------------------------------------- application
const app = createApp(App)

// ---------------------------------------------------------------------------------------------------------- components
app.component("InputText", InputText);
app.component("Button", Button);
app.component("Divider", Divider);
app.component("TabMenu", TabMenu);
app.component("Menu", Menu);
app.component("Sidebar", Sidebar);
app.component("Splitter", Splitter);
app.component("SplitterPanel", SplitterPanel);
app.component("ConfirmPopup", ConfirmPopup);
app.component("RadioButton", RadioButton);
app.component("Dropdown", Dropdown);
app.component("Textarea", Textarea);
app.component("OverlayPanel", OverlayPanel);
app.component("Badge", Badge);
app.component("Fieldset", Fieldset);
app.component("InputSwitch", InputSwitch);
app.component("Checkbox", Checkbox);
app.component("Toast", Toast);
app.component("ProgressSpinner", ProgressSpinner);
app.component("Skeleton", Skeleton);
app.component("ToggleButton", ToggleButton);
app.component("SelectButton", SelectButton);
app.component("ColorPicker", ColorPicker);
app.component("Dialog", Dialog);
app.component("Chips", Chips);
app.component("DataView", DataView);
app.component("TabView", TabView);
app.component("TabPanel", TabPanel);
app.component("Image", Image);
app.component("MultiSelect", MultiSelect);
app.component("AutoComplete", AutoComplete);
app.component("Slider", Slider);
app.directive('tooltip', Tooltip);
app.directive('badge', BadgeDirective);

const options = {
  color: "#324bed",
  failedColor: "#ff0000",
  thickness: "2px",
  transition: {
    speed: "0.2s",
    opacity: "0.6s",
    termination: 300,
  },
  autoRevert: true,
  location: "top",
  inverse: false,
};
app.use(VueProgressBar, options);

library.add(faBold, faItalic, faStrikethrough, faParagraph, faHighlighter, faListOl, faListUl, faTextSlash, faRectangleXmark);
library.add(faCircleChevronLeft, faCircleChevronRight, faPeopleGroup, faFileExport, faFileImport, faLink, faArrowUpRightFromSquare);
library.add(faBoltLightning, faCloudArrowDown, faSquare, faTableCells, faTableCellsLarge, faHome, faToggleOn, faToggleOff, faCodeFork);
library.add(faDiagramProject, faChevronCircleDown, faCircleNotch, faCirclePlus, faCircleMinus, faChevronDown, faChevronLeft, faChevronRight, faCheck);
library.add(faCode, faTerminal, faQuoteRight, faTimeline, faSun, faMoon, faRobot, faUserSecret, faArrowAltCircleDown, faArrowAltCircleRight);
library.add(faArrowAltCircleDownSolid, faArrowAltCircleRightSolid, faAlignLeft, faAlignRight, faAlignCenter, faLinkSlash, faMarkdown, faFilePdf, faFileArrowUp);
library.add(faUser, faUserGear, faGlasses, faUserXmark );

// ----------------------------------------------------------------------------------------- wrapping up the application

app.use(VueShowdownPlugin, {
  options: {
    emoji: true,
  },
});

app.use(ganttastic);

app.component('font-awesome-icon', FontAwesomeIcon);
app.use(createPinia())
app.use(router)
app.use(PrimeVue, {ripple: true});
app.use(ConfirmationService);
app.use(ToastService);

app.mount('#app')
