import autoComplete from "@tarekraafat/autocomplete.js";

window.addEventListener('DOMContentLoaded', () => {
  if (null != document.querySelector('input.address-autocomplete')) {
    const autoCompleteJS = new autoComplete({
      selector: 'input.address-autocomplete',
      wrapper: false,
      data: {
        src: async (query) => {
          try {
            // Fetch Data from external Source
            const source = await fetch(`https://photon.komoot.io/api/?q=${query}&lang=fr&layer=house&layer=street&layer=city`);
            // Data should be an array of `Objects` or `Strings`
            const results = await source.json();

            const data = [];
            results.features.forEach((result) => {
              let displayText = result.properties.postcode + " " + result.properties.city;
              let addressLine1 = '';

              if (result.properties.type == 'house') {
                addressLine1 = result.properties.housenumber + " " + result.properties.street;
                displayText = addressLine1 + " " + displayText;
              } else if (result.properties.type == 'street') {
                addressLine1 = result.properties.name;
                displayText = addressLine1 + " " + displayText;
              }

              result.displayText = displayText;
              result.addressLine1 = addressLine1;
              data.push(result);
            })

            return data;
          } catch (error) {
            return error;
          }
        },
        keys: ['displayText'],
      },
      threshold: 5,
      debounce: 300,
      maxResults: 15,
      searchEngine: (query, record) => {
        return record;
      },
      events: {
        input: {
          selection: (event) => {
            const selection = event.detail.selection.value;
            event.target.value = selection.displayText;

            // Remove the "[address]" part at then end of this input name
            const formBaseName = event.target.name.substring(0, event.target.name.length - 9);
            document.querySelector('input[name="'+formBaseName+'[countryCode]"]').value = selection.properties.countrycode;
            document.querySelector('input[name="'+formBaseName+'[addressLine1]"]').value = selection.addressLine1;
            document.querySelector('input[name="'+formBaseName+'[zipCode]"]').value = selection.properties.postcode;
            document.querySelector('input[name="'+formBaseName+'[city]"]').value = selection.properties.city;
            document.querySelector('input[name="'+formBaseName+'[latitude]"]').value = selection.geometry.coordinates[1];
            document.querySelector('input[name="'+formBaseName+'[longitude]"]').value = selection.geometry.coordinates[0];
          },
        }
      }
    })
  }
})
