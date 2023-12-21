<script setup>
    import { onMounted, ref, computed } from 'vue';
    import axios from 'axios';
    import { loadLanguageAsync, getActiveLanguage } from 'laravel-vue-i18n';
    import { usePage } from '@inertiajs/vue3';

    import '../../../node_modules/admin-lte/plugins/flag-icon-css/css/flag-icons.min.css';

    const responsiveSettingsLanguage = ref(false);
    //const act_locale = ref('en');

    // Backend felől jövő paraméterek
    const props = usePage().props;
    // Hitelesített felhasználó
    const user = props.auth.user;
    // Engedélyezett nyelvek
    const languages = props.languages;
    // Aktuálisan beállított nyelv
    const act_locale = ref(props.locale);

    // Nyelvváltoztatás küldése a Backend felé
    function setLocale(locale) {
        axios.post(route('language'), {locale: locale})
        .then((response) => {
            //console.log('Változás után');
            //console.log('új nyelv: ' + locale);
            //console.log(getActiveLanguage());
            // Új nyelv betöltése
            loadLanguageAsync(locale);
            // Aktuális nyelv eltárolása
            act_locale.value = locale;
        })
        .catch((error) => {
            console.log(error);
        });
    };

    onMounted(() => {
        // Aktuális nyelv eltárolása
        act_locale.value = props.locale;
        // Nyelv betöltése
        loadLanguageAsync(act_locale.value);

    });
/*
    {{ $page.props.locale }}
    {{ $page.props.languages }}
    {{ $page.props.languageOptions }}
*/
</script>
<template>

    <li class="nav-item dropdown">
        <a class="nav-link" href="#" data-toggle="dropdown" 
           @click="responsiveSettingsLanguage = !responsiveSettingsLanguage">
            <i class="flag-icon" :class="`flag-icon-${act_locale}`"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right p-0">
            <a href="#" 
               @click="setLocale(key)"
               class="dropdown-item"
               :class="( act_locale == key ) ? 'active' : ''" 
               v-for="(key, lang) in languages"
            ><i class="flag-icon mr-2" 
                :class="`flag-icon-${key}`"></i>{{ lang }}</a>
        </div>
    </li>
</template>