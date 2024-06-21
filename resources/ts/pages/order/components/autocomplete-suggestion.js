class autocompleteSuggestion {

  constructor() {
  }

  static render(data) {
    const suggestionElement = document.createElement('div');
    suggestionElement.classList.add('autocomplete-suggestion');
    suggestionElement.innerHTML = `
                          <div><strong>${suggestion.name}</strong></div>
                          <div><span class="informacoes-cliente"><i class="fas fa-envelope"></i> ${data.email ? data.email : 'Não informado'}</span></div>
                          <div><span class="informacoes-cliente"><i class="fas fa-phone"></i> ${data.phone ? data.phone : 'Não informado'}</span></div>
                          <div class="divider"></div>
                      `;
    return suggestionElement;
  }
}
