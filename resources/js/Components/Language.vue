<script setup>
    import { onMounted, ref } from 'vue';
    import axios from 'axios';
    import { loadLanguageAsync, getActiveLanguage } from 'laravel-vue-i18n';

    import '../../../node_modules/admin-lte/plugins/flag-icon-css/css/flag-icons.min.css';

    const responsiveSettingsLanguage = ref(false);
    const act_locale = ref('en');

    function setLocale(locale) {
        axios.post(route('language'), {locale: locale})
        .then((response) => {
            loadLanguageAsync(locale);
            //act_locale.value = getActiveLanguage();
            act_locale.value = locale;
        })
        .catch((error) => {
            console.log(error);
        });
    };

    onMounted(() => {
        console.log(getActiveLanguage());
    });

</script>
<template>
    <li class="nav-item dropdown">
        <a class="nav-link" href="#" data-toggle="dropdown" 
           @click="responsiveSettingsLanguage = !responsiveSettingsLanguage">
            <i v-if="act_locale == 'en'" class="flag-icon flag-icon-us"></i>
            <i v-else class="flag-icon flag-icon-hu"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right p-0">
            <a href="#" 
               @click="setLocale('en')" 
               class="dropdown-item"
               :class="( act_locale == 'en' ) ? 'active' : ''">
                <i class="flag-icon flag-icon-us mr-2"></i>&nbsp;English
            </a>
            <a href="#" @click="setLocale('hu')" 
               class="dropdown-item"
               :class="( act_locale == 'hu' ) ? 'active' : ''">
                <i class="flag-icon flag-icon-hu mr-2"></i>&nbsp;Hungarian
            </a>
        </div>
    </li>
</template>