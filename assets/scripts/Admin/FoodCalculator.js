class FoodCalculator {
  constructor(table) {
    this.table = table;
    this.startCell = null;
    this.endCell = null;

    table.querySelectorAll('td').forEach((cell) => {
      cell.addEventListener('click', () => {
        if (!this.startCell) {
          this.startCell = cell;
          cell.classList.add("table-warning");
        } else if (!this.endCell) {
          this.endCell = cell;
          cell.classList.add("table-warning");

          this.updateRecap();
        } else {
          this.reset();
        }
      });
    });
  }

  updateRecap() {
    const meals = this.calculateSelectedMeals();

    let totalBread = 0;
    let totalCheese = 0;
    let totalVeggies = 0;
    let totalFruits = 0;

    for (const { meal, quantity } of meals) {
      switch (meal) {
        case 'breakfast':
          totalBread += quantity * 100;
          break;
        case 'lunch':
        case 'dinner':
          totalBread += quantity * 40;
          totalCheese += quantity * 40;
          totalVeggies += quantity * 250;
          totalFruits += quantity * 125;
          break;
      }
    }

    document.getElementById('recap-container').classList.remove('d-none');
    document.getElementById('qty-bread').textContent = `${this.round(totalBread / 1000)} kg`;
    document.getElementById('qty-cheese').textContent = `${this.round(totalCheese / 1000)} kg`;
    document.getElementById('qty-veggies').textContent = `${this.round(totalVeggies / 1000)} kg`;
    document.getElementById('qty-fruits').textContent = `${this.round(totalFruits / 1000)} kg`;
  }

  reset() {
    document.getElementById('recap-container').classList.add('d-none');
    this.table.querySelectorAll('td').forEach((td) => td.classList.remove("table-warning"));
    this.startCell = null;
    this.endCell = null;
  }

  calculateSelectedMeals() {
    const allRows = [...this.startCell.closest("tbody").children];
    const startPos = this.getCellPosition(this.startCell);
    const endPos = this.getCellPosition(this.endCell);
    const startIndex = startPos.col * allRows.length + startPos.row;
    const endIndex = endPos.col * allRows.length + endPos.row;
    const [start, end] = [startIndex, endIndex].sort((a, b) => a - b);

    const meals = [];

    for (let index = start; index <= end; index++) {
      const row = index % allRows.length;
      const col = Math.floor(index / allRows.length);
      const cell = allRows[row].children[col];

      cell?.classList.add("table-warning");

      const val = parseInt(cell?.textContent.trim());
      if (!isNaN(val)) {
        const meal = cell.dataset.meal || 'unknown';
        meals.push({ meal, quantity: val });
      }
    }

    return meals;
  }

  getCellPosition(cell) {
    const row = [...cell.parentNode.parentNode.children].indexOf(cell.parentNode);
    const col = [...cell.parentNode.children].indexOf(cell);
    return { row, col };
  }

  round(num) {
    return Math.round((num + Number.EPSILON) * 100) / 100;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  if (null !== document.querySelector('table.food-calculator')) {
    (() => new FoodCalculator(document.querySelector('table.food-calculator')))();
  };
});
