// Get dynamic data from PHP (passed via Twig template)
const INITIAL_JOBS = window.jobsData || [];
const TOTAL_JOBS = window.totalJobs || 0;
const STUDENT_DATA = window.studentData || null;

const state = {
  filters: {
    searchQuery: '',
    company: '',
    contractType: ''
  }
};

const Navbar = () => {
  const student = window.studentData || {};

  return `
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
      <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">

        <!-- Logo -->
        <div class="flex items-center gap-2">
          <div class="bg-primary size-9 rounded-lg flex items-center justify-center text-white">
            <span class="material-symbols-outlined">handshake</span>
          </div>
          <span class="text-xl font-bold tracking-tight text-slate-900">
            YouCode<span class="text-primary">.</span>
          </span>
        </div>

        <!-- Student Info -->
        <div class="flex items-center gap-3 cursor-pointer group">
          <div class="size-10 rounded-full border-2 border-slate-100 p-0.5 group-hover:border-primary transition-all">
            <img
              alt="User"
              class="w-full h-full rounded-full bg-slate-100 object-cover"
              src="https://picsum.photos/seed/user123/100/100"
            />
          </div>
          <div class="hidden sm:block text-right">
            <p class="text-xs font-bold text-slate-900 leading-none mb-0.5">
              ${student.name ?? 'Guest'}
            </p>
            <p class="text-[10px] text-slate-500 font-medium">
              ${student.promotion ? `Promo ${student.promotion}` : 'Promo 2024'}
            </p>
          </div>
        </div>

      </div>
    </nav>
  `;
};


const Hero = () => `
  <header class="bg-soft-bg pt-20 pb-32">
    <div class="max-w-4xl mx-auto px-6 text-center">
      <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white shadow-sm mb-6 border border-slate-100">
        <span class="flex h-2 w-2 rounded-full bg-primary animate-pulse"></span>
        <span class="text-xs font-bold text-slate-600 uppercase tracking-widest">${TOTAL_JOBS} New Opportunities Today</span>
      </div>
      <h1 class="font-poppins text-4xl md:text-5xl lg:text-6xl font-bold text-slate-900 mb-6 leading-[1.15]">
        Find Your Next <br />
        <span class="text-primary italic">Opportunity</span>
      </h1>
      <p class="text-lg text-slate-500 max-w-2xl mx-auto leading-relaxed">
        Connect with leading tech companies and launch your career. Specialized roles for YouCode students and alumni.
      </p>
    </div>
  </header>
`;

const SearchSection = () => {
  // Get unique companies from jobs data
  const companies = [...new Set(INITIAL_JOBS.map(job => job.company_name || job.company))].filter(Boolean);
  const contractTypes = [...new Set(INITIAL_JOBS.map(job => job.contract_type || job.type))].filter(Boolean);

  return `
  <div class="max-w-7xl mx-auto px-6 -mt-10 relative z-10">
    <div class="bg-white p-4 rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 flex flex-col lg:flex-row gap-4">
      <div class="flex-1 relative">
        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
        <input
          id="searchQuery"
          class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm placeholder:text-slate-400 transition-all outline-none"
          placeholder="Search by job title or keywords..."
          type="text"
          value="${state.filters.searchQuery}"
        />
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <div class="relative min-w-[160px]">
          <select
            id="companyFilter"
            class="w-full py-3.5 pl-4 pr-10 bg-slate-50 border-none text-slate-600 text-sm font-medium rounded-xl focus:ring-2 focus:ring-primary/20 cursor-pointer outline-none"
          >
            <option value="">All Companies</option>
            ${companies.map(company => `
              <option value="${company}" ${state.filters.company === company ? 'selected' : ''}>${company}</option>
            `).join('')}
          </select>
        </div>
        <div class="relative min-w-[160px]">
          <select
            id="typeFilter"
            class="w-full py-3.5 pl-4 pr-10 bg-slate-50 border-none text-slate-600 text-sm font-medium rounded-xl focus:ring-2 focus:ring-primary/20 cursor-pointer outline-none"
          >
            <option value="">Contract Type</option>
            ${contractTypes.map(type => `
              <option value="${type}" ${state.filters.contractType === type ? 'selected' : ''}>${type}</option>
            `).join('')}
          </select>
        </div>
        <button id="searchBtn" class="bg-primary hover:bg-blue-600 active:scale-95 text-white font-bold text-sm px-8 py-3.5 rounded-xl shadow-lg shadow-primary/30 transition-all flex items-center gap-2">
          <span class="material-symbols-outlined text-[18px]">tune</span>
          Search
        </button>
      </div>
    </div>
  </div>
`;
};

const JobCard = (job) => {
  // Support both database structure and old structure
  const title = job.title || job.job_title || 'Untitled';
  const company = job.company_name || job.company || 'Unknown Company';
  const location = job.location || job.job_location || 'Unknown';
  const type = job.contract_type || job.type || 'Full-time';
  const description = job.description || job.job_description || '';
  const logoUrl = job.logo_url || job.logoUrl || 'https://picsum.photos/seed/' + company + '/100/100';
  const postedAt = job.posted_at || job.postedAt || 'Recently';

  return `
  <div class="bg-white rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-[0_10px_30px_-10px_rgba(59,130,246,0.15)] transition-all duration-300 border border-slate-100 p-6 flex flex-col h-full">
    <div class="flex items-start justify-between mb-6">
      <div class="size-14 bg-slate-50 rounded-xl flex items-center justify-center border border-slate-100 overflow-hidden">
        <img alt="${company}" class="w-full h-full object-cover" src="${logoUrl}" />
      </div>
      <span class="inline-flex items-center px-3 py-1 rounded-full bg-primary/10 text-primary text-[11px] font-bold uppercase tracking-wider">
        ${type}
      </span>
    </div>
    <div class="mb-4">
      <h3 class="font-poppins font-bold text-xl text-slate-900 mb-1 leading-tight">${title}</h3>
      <p class="text-sm font-medium text-slate-500">${company} • ${location}</p>
    </div>
    <p class="text-sm text-slate-500 line-clamp-2 leading-relaxed mb-6">
      ${description}
    </p>
    <div class="mt-auto pt-6 border-t border-slate-50 flex items-center justify-between">
      <div class="flex items-center gap-1.5 text-slate-400">
        <span class="material-symbols-outlined text-[18px]">schedule</span>
        <span class="text-xs font-medium">${postedAt}</span>
      </div>
      <button class="text-primary font-bold text-sm hover:underline flex items-center gap-1 transition-all group">
        View Details
        <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
      </button>
    </div>
  </div>
`;
};

const Footer = () => `
  <footer class="bg-slate-900 text-white py-20 mt-auto">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
      <div class="col-span-1 md:col-span-1">
        <div class="flex items-center gap-2 mb-6">
          <div class="bg-primary size-8 rounded-lg flex items-center justify-center text-white">
            <span class="material-symbols-outlined text-[20px]">handshake</span>
          </div>
          <span class="text-xl font-bold tracking-tight">YouCode.</span>
        </div>
        <p class="text-slate-400 text-sm leading-relaxed">
          The exclusive job dating platform for YouCode students. Connecting talent with opportunity.
        </p>
      </div>
      <div>
        <h4 class="font-bold text-sm uppercase tracking-widest mb-6 text-slate-500">For Students</h4>
        <ul class="space-y-4">
          <li><a class="text-sm text-slate-400 hover:text-white transition-colors" href="#">Career Advice</a></li>
          <li><a class="text-sm text-slate-400 hover:text-white transition-colors" href="#">Resume Tips</a></li>
          <li><a class="text-sm text-slate-400 hover:text-white transition-colors" href="#">Success Stories</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-bold text-sm uppercase tracking-widest mb-6 text-slate-500">Platform</h4>
        <ul class="space-y-4">
          <li><a class="text-sm text-slate-400 hover:text-white transition-colors" href="#">Privacy Policy</a></li>
          <li><a class="text-sm text-slate-400 hover:text-white transition-colors" href="#">Terms of Use</a></li>
          <li><a class="text-sm text-slate-400 hover:text-white transition-colors" href="#">Contact Support</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-bold text-sm uppercase tracking-widest mb-6 text-slate-500">Subscribe</h4>
        <p class="text-sm text-slate-400 mb-4">Get the latest job alerts in your inbox.</p>
        <div class="flex gap-2">
          <input 
            class="bg-slate-800 border-none rounded-lg text-sm px-4 py-2.5 w-full focus:ring-1 focus:ring-primary outline-none" 
            placeholder="Email address" 
            type="email" 
          />
          <button class="bg-primary hover:bg-blue-600 px-5 py-2.5 rounded-lg text-sm font-bold transition-all">Join</button>
        </div>
      </div>
    </div>
    <div class="max-w-7xl mx-auto px-6 pt-16 mt-16 border-t border-slate-800 flex flex-col md:flex-row items-center justify-between gap-6">
      <p class="text-slate-500 text-xs">© 2023 YouCode Job Dating. All rights reserved.</p>
      <div class="flex items-center gap-6">
        <a class="text-slate-500 hover:text-white transition-colors" href="#"><span class="material-symbols-outlined text-[20px]">public</span></a>
        <a class="text-slate-500 hover:text-white transition-colors" href="#"><span class="material-symbols-outlined text-[20px]">alternate_email</span></a>
      </div>
    </div>
  </footer>
`;

function render() {
  const root = document.getElementById('root');
  if (!root) return;

  const filteredJobs = INITIAL_JOBS.filter(job => {
    const title = job.title || job.job_title || '';
    const description = job.description || job.job_description || '';
    const company = job.company_name || job.company || '';
    const type = job.contract_type || job.type || '';

    const matchesSearch = title.toLowerCase().includes(state.filters.searchQuery.toLowerCase()) ||
                        description.toLowerCase().includes(state.filters.searchQuery.toLowerCase());
    const matchesCompany = state.filters.company === '' || company === state.filters.company;
    const matchesType = state.filters.contractType === '' || type === state.filters.contractType;
    return matchesSearch && matchesCompany && matchesType;
  });

  const isFiltered = state.filters.searchQuery || state.filters.company || state.filters.contractType;

  root.innerHTML = `
    <div class="min-h-screen flex flex-col bg-white">
      ${Navbar()}
      
      <main class="flex-grow">
        ${Hero()}
        
        ${SearchSection()}

        <section class="max-w-7xl mx-auto px-6 py-16">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-10">
            <h2 class="text-2xl font-bold text-slate-900 font-poppins">
              ${isFiltered ? `Search Results (${filteredJobs.length})` : 'Recommended Jobs'}
            </h2>
            <div class="flex items-center gap-2">
              <span class="text-sm font-medium text-slate-500">Sort by:</span>
              <button class="text-sm font-bold text-slate-900 flex items-center gap-1 group">
                Newest First
                <span class="material-symbols-outlined text-[18px] group-hover:translate-y-0.5 transition-transform">expand_more</span>
              </button>
            </div>
          </div>

          ${filteredJobs.length > 0 ? `
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              ${filteredJobs.map(job => JobCard(job)).join('')}
            </div>
          ` : `
            <div class="py-20 text-center">
              <div class="bg-slate-50 size-20 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-slate-300 text-4xl">search_off</span>
              </div>
              <h3 class="text-xl font-bold text-slate-900 mb-2">No jobs found</h3>
              <p class="text-slate-500">Try adjusting your filters or search query.</p>
              <button 
                id="clearFiltersBtn"
                class="mt-6 text-primary font-bold hover:underline"
              >
                Clear all filters
              </button>
            </div>
          `}

          <div class="mt-16 flex items-center justify-center gap-2">
            <button class="size-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:text-primary transition-all">
              <span class="material-symbols-outlined">chevron_left</span>
            </button>
            <button class="size-10 flex items-center justify-center rounded-xl bg-primary text-white font-bold shadow-lg shadow-primary/20">1</button>
            <button class="size-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-600 font-bold hover:bg-slate-100 transition-all">2</button>
            <button class="size-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-600 font-bold hover:bg-slate-100 transition-all">3</button>
            <span class="px-2 text-slate-300">...</span>
            <button class="size-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-600 font-bold hover:bg-slate-100 transition-all">12</button>
            <button class="size-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:text-primary transition-all">
              <span class="material-symbols-outlined">chevron_right</span>
            </button>
          </div>
        </section>
      </main>

      ${Footer()}
    </div>
  `;

  document.getElementById('searchQuery')?.addEventListener('input', (e) => {
    state.filters.searchQuery = e.target.value;
  });

  document.getElementById('companyFilter')?.addEventListener('change', (e) => {
    state.filters.company = e.target.value;
    render();
  });

  document.getElementById('typeFilter')?.addEventListener('change', (e) => {
    state.filters.contractType = e.target.value;
    render();
  });

  document.getElementById('searchBtn')?.addEventListener('click', () => {
    render();
  });

  document.getElementById('clearFiltersBtn')?.addEventListener('click', () => {
    state.filters = { searchQuery: '', company: '', contractType: '' };
    render();
  });

  document.getElementById('searchQuery')?.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
      render();
    }
  });

  const qInput = document.getElementById('searchQuery');
  if (qInput && state.filters.searchQuery) {
    qInput.focus();
    const len = qInput.value.length;
    qInput.setSelectionRange(len, len);
  }
}

document.addEventListener('DOMContentLoaded', render);
