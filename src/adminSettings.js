/**
 * Nextcloud - OVH
 *
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier <julien-nc@posteo.net>
 * @copyright Julien Veyssier 2024
 */

import AdminSettings from './components/AdminSettings.vue'
import { createApp } from 'vue'

const app = createApp(AdminSettings)
app.mount('#ovhai_prefs')
