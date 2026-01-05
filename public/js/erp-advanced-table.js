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

    // Page numbers with ellipsis
    const pages = [];
    const delta = 2;

    for (let i = 1; i <= total; i++) {
      if (
        i === 1 ||
        i === total ||
        (i >= current - delta && i <= current + delta)
      ) {
        pages.push(i);
      }
    }

    let last = null;
    for (let i of pages) {
      if (last !== null) {
        if (i - last === 2) {
          const p = last + 1;
          ul.innerHTML += `
                <li class="page-item ${p === current ? "active" : ""}">
                    <a class="page-link" href="#"
                       onclick="erpTable.page('${id}', ${p})">${p}</a>
                </li>
            `;
        } else if (i - last > 2) {
          ul.innerHTML += `
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            `;
        }
      }
      ul.innerHTML += `
            <li class="page-item ${i === current ? "active" : ""}">
                <a class="page-link" href="#"
                   onclick="erpTable.page('${id}', ${i})">${i}</a>
            </li>
        `;
      last = i;
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
