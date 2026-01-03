window.erpTable = {
  state: {},

  init(id, endpoint, searchFields, defaultHidden = [], defaultFilters = {}) {
    this.state[id] = {
      endpoint,
      searchFields,
      page: 1,
      limit: 10,
      search: "",
      filters: defaultFilters,
      hiddenColumns: defaultHidden,
    };
    this.renderStyles(id);
    this.load(id);
  },

  search(id, value) {
    this.state[id].search = value;
    this.state[id].page = 1;
    this.load(id);
  },

  filter(id, key, value) {
    this.state[id].filters[key] = value;
    this.state[id].page = 1;
    this.load(id);
  },

  setLimit(id, limit) {
    this.state[id].limit = limit;
    this.state[id].page = 1;
    this.load(id);
  },

  toggleColumn(id, key) {
    const s = this.state[id];
    const idx = s.hiddenColumns.indexOf(key);
    if (idx === -1) {
      s.hiddenColumns.push(key);
    } else {
      s.hiddenColumns.splice(idx, 1);
    }
    this.renderStyles(id);
  },

  page(id, page) {
    this.state[id].page = page;
    this.load(id);
  },

  load(id) {
    const s = this.state[id];
    const params = new URLSearchParams({
      page: s.page,
      limit: s.limit,
      search: s.search,
      ...s.filters,
    });

    fetch(`${s.endpoint}?${params}`)
      .then((r) => r.json())
      .then((res) => {
        document.querySelector(`#${id} tbody`).innerHTML = res.rows;
        this.renderPagination(id, res.pagination);
      });
  },

  renderPagination(id, p) {
    const ul = document.getElementById(id + "_pagination");
    ul.innerHTML = "";

    const current = p.current;
    const total = p.total;
    const maxVisible = 5;

    if (total <= 1) return;

    // Previous
    ul.innerHTML += `
        <li class="page-item ${current === 1 ? "disabled" : ""}">
            <a class="page-link" href="#"
               onclick="erpTable.page('${id}', ${current - 1})">Previous</a>
        </li>
    `;

    // Calculate range
    let start = Math.max(1, current - Math.floor(maxVisible / 2));
    let end = start + maxVisible - 1;

    if (end > total) {
      end = total;
      start = Math.max(1, end - maxVisible + 1);
    }

    // Page numbers
    for (let i = start; i <= end; i++) {
      ul.innerHTML += `
            <li class="page-item ${i === current ? "active" : ""}">
                <a class="page-link" href="#"
                   onclick="erpTable.page('${id}', ${i})">${i}</a>
            </li>
        `;
    }

    // Next
    ul.innerHTML += `
        <li class="page-item ${current === total ? "disabled" : ""}">
            <a class="page-link" href="#"
               onclick="erpTable.page('${id}', ${current + 1})">Next</a>
        </li>
    `;
  },

  renderStyles(id) {
    let style = document.getElementById(`style-${id}`);
    if (!style) {
      style = document.createElement("style");
      style.id = `style-${id}`;
      document.head.appendChild(style);
    }
    const s = this.state[id];
    const css = s.hiddenColumns
      .map((key) => `#${id} .col-${key} { display: none !important; }`)
      .join("\n");
    style.textContent = css;
  },
};
