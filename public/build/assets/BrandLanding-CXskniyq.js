import{j as e,L as j,R as h,H as N}from"./app-BxQh7MNn.js";import{A as w,N as v,P as k,g as S,M as _}from"./MainLayout-Dy0CagaB.js";import{S as p,a as x,W as P}from"./WorkShowcaseSection-CW8KqLKp.js";import{S as y}from"./SectionTitle-B-dp7Dbj.js";/* empty css            */import"./Toast-CtWqPcwO.js";function C({brand:s,pageData:r,relatedServices:t,getImageUrl:i,setLightboxImage:a}){const m=(n,l)=>{n.preventDefault();const c=document.querySelector(l);if(c){const g=document.body.getBoundingClientRect().top,o=c.getBoundingClientRect().top-g-100;window.scrollTo({top:o,behavior:"smooth"})}};return e.jsxs("section",{children:[e.jsxs("div",{className:"brand-hero-area position-relative",style:{backgroundColor:r.hero_styles?.background_color||"#E8B4B4",backgroundImage:r.hero_styles?.background_image?`url(${i(r.hero_styles.background_image)})`:"none",backgroundSize:"cover",backgroundPosition:"center",padding:"100px 0",minHeight:"500px",display:"flex",alignItems:"center"},children:[e.jsx("div",{className:"position-absolute top-0 start-0 w-100 h-100",style:{backgroundColor:"rgba(15, 23, 42, 0.6)",zIndex:0}}),e.jsx("div",{className:"container th-container position-relative",style:{zIndex:1},children:e.jsxs("div",{className:"row align-items-center",children:[e.jsx("div",{className:"col-lg-6",children:e.jsxs("div",{className:"hero-content wow fadeInUp",children:[e.jsx("span",{className:"sub-title text-uppercase mb-20",style:{letterSpacing:"2px"},children:"Business Solution"}),e.jsx("h1",{className:"hero-title text-anime-style-3 mb-4",style:{fontSize:"clamp(2rem, 5vw, 4rem)",fontWeight:"bold",color:"white",lineHeight:"1.2"},children:s.name.toUpperCase()}),e.jsx("p",{className:"hero-text mb-30",style:{fontSize:"1.2rem",maxWidth:"500px",color:"rgba(255,255,255,0.8)"},children:"Explore video conferencing products, including conference cameras, room solutions, webcams, headsets, collaboration tools, and accessories."}),e.jsxs("a",{href:"#products",className:"th-btn style3 th-radius th-icon",children:["Contact Sales"," ",e.jsx("i",{className:"fa-regular fa-arrow-right ms-2"})]})]})}),e.jsx("div",{className:"col-lg-6",children:e.jsxs("div",{className:"hero-scroll-cards wow fadeInRight",style:{maxWidth:"800px",marginLeft:"auto"},children:[e.jsx("div",{className:"mb-4 ps-0 ps-lg-2 text-center text-lg-start",children:e.jsxs("span",{className:"sub-title style1 text-anime-style-2 text-white",children:[e.jsx("span",{className:"squre-shape left me-3 bg-white"}),"Award & Certified",e.jsx("span",{className:"squre-shape d-lg-none right ms-3 bg-white"})]})}),e.jsx(p,{modules:[w],spaceBetween:20,autoplay:{delay:2500,disableOnInteraction:!1},loop:!0,breakpoints:{0:{slidesPerView:1},768:{slidesPerView:2},1024:{slidesPerView:3}},className:"hero-badge-slider",children:[1,2,3,4].map(n=>e.jsx(x,{children:e.jsx("div",{className:"bg-white d-flex align-items-center justify-content-center shadow-lg",style:{width:"250px",height:"250px",padding:"20px",margin:"0 auto",borderRadius:"10px",cursor:"pointer"},onClick:()=>a("https://activ.co.id/wp-content/uploads/2024/11/elite-dk-360x360-1.png"),children:e.jsx("img",{src:"https://activ.co.id/wp-content/uploads/2024/11/elite-dk-360x360-1.png",alt:"Elite Partner",className:"img-fluid",style:{maxHeight:"100%",maxWidth:"100%",objectFit:"contain"}})})},n))})]})})]})})]}),e.jsx("div",{className:"brand-subnav py-3",style:{backgroundColor:"#1DA2C0"},children:e.jsx("div",{className:"container th-container",children:e.jsx("div",{className:"d-flex justify-content-center gap-4 flex-wrap",children:(()=>{const n=[];return t&&t.length>0?t.forEach(l=>{n.push({name:l.name,link:`#service-${l.slug}`})}):n.push({name:"Solutions",link:"#room-solutions"}),n.push({name:"Kategori",link:"#all-categories"},{name:"New Product",link:"#latest-products"},{name:"Project",link:"#project-showcase"}),n.map((l,c)=>e.jsx("a",{href:l.link,onClick:d=>m(d,l.link),className:"text-white text-uppercase fw-bold text-decoration-none fs-6 hover-opacity",children:l.name},c))})()})})})]})}function $({subTitle:s,title:r,align:t="text-center text-lg-start",mb:i="mb-20"}){return e.jsxs("div",{className:`title-area ${t} ${i}`,children:[s&&e.jsxs("span",{className:"sub-title style1 text-anime-style-2",children:[e.jsx("span",{className:"squre-shape left me-3"}),s,e.jsx("span",{className:"squre-shape d-lg-none right ms-3"})]}),e.jsx("h2",{className:"sec-title text-white",children:r})]})}function B({relatedServices:s,getImageUrl:r}){return!s||s.length===0?null:e.jsx(e.Fragment,{children:s.map((t,i)=>e.jsxs("section",{className:"position-relative space-top space-extra-bottom",id:`service-${t.slug}`,style:{backgroundImage:`url(${t.image?r(t.image):"/assets/img/bg/bg_overlay_1.png"})`,backgroundSize:"cover",backgroundPosition:"center"},children:[e.jsx("div",{className:"position-absolute top-0 start-0 w-100 h-100",style:{background:"linear-gradient(to bottom, #0A141E 0%, rgba(10, 20, 30, 0.85) 15%, rgba(10, 20, 30, 0.85) 85%, #0A141E 100%)",zIndex:1}}),e.jsxs("div",{className:"container th-container position-relative",style:{zIndex:2},children:[e.jsx("div",{className:"row justify-content-center",children:e.jsx("div",{className:"col-xl-8",children:e.jsx("div",{className:"title-area text-center mb-55",children:e.jsx($,{subTitle:t.sub_title,title:t.title,align:"title-area service-title-box text-center",subTitleStyle:{backgroundColor:"transparent",color:"#20B2AA"}})})})}),e.jsxs("div",{className:"slider-area",children:[e.jsx(p,{modules:[v,k],spaceBetween:30,slidesPerView:1,pagination:{el:`.service-pagination-${i}`,type:"progressbar"},navigation:{prevEl:`.service-prev-${i}`,nextEl:`.service-next-${i}`},onBeforeInit:a=>{a.params.navigation.prevEl=`.service-prev-${i}`,a.params.navigation.nextEl=`.service-next-${i}`},breakpoints:{576:{slidesPerView:1},768:{slidesPerView:2},992:{slidesPerView:3},1200:{slidesPerView:3}},className:"service-slider",children:t.solutions.map((a,m)=>e.jsx(x,{children:e.jsx("div",{className:"room-card position-relative overflow-hidden rounded-20 bg-dark",children:e.jsxs("div",{className:"room-img position-relative overflow-hidden room-img-height",style:{borderRadius:"20px"},children:[e.jsx("img",{src:a.image?r(a.image):"/assets/img/logo/activ-logo-white.png",onError:n=>{n.target.onerror=null,n.target.src="https://placehold.co/600x400?text=No+Image"},alt:a.title,className:"w-100 h-100 object-fit-cover",style:{transition:"transform 0.5s ease"}}),e.jsxs("div",{className:"position-absolute bottom-0 start-0 w-100 p-4",style:{background:"linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 60%, transparent 100%)",zIndex:2},children:[e.jsx("h3",{className:"text-white mb-2 h4 fw-bold",children:a.title}),e.jsx("p",{className:"text-white-50 mb-4 fs-xs",children:a.desc}),e.jsxs(j,{href:`/services/${t.slug}/${a.slug}`,className:"th-btn style3 th-radius th-icon room-btn btn-ghost-green",children:["Show Products"," ",e.jsx("i",{className:"fa-regular fa-arrow-right ms-2"})]})]})]})})},m))}),e.jsxs("div",{className:"d-none d-md-flex align-items-center justify-content-center gap-4 mt-5",children:[e.jsx("button",{className:`service-prev-${i} icon-btn style2 bg-transparent border rounded-circle border-white text-white hover-white`,children:e.jsx("i",{className:"fa-regular fa-arrow-left"})}),e.jsx("div",{className:`service-pagination-${i} position-relative`,style:{width:"150px",height:"2px",background:"rgba(255,255,255,0.2)",borderRadius:"2px",overflow:"hidden"}}),e.jsx("button",{className:`service-next-${i} icon-btn style2 bg-transparent border rounded-circle border-white text-white hover-white`,children:e.jsx("i",{className:"fa-regular fa-arrow-right"})})]})]})]})]},i))})}function E({categories:s,brand:r,getBrandSlug:t,getImageUrl:i}){return e.jsx("section",{className:"space",id:"all-categories",children:e.jsxs("div",{className:"container th-container",children:[e.jsx("div",{className:"title-area text-start mb-40",children:e.jsx(y,{subTitle:"Kategori",title:"Semua Kategori",align:"title-area service-title-box text-center"})}),e.jsx("div",{className:"row justify-content-center gy-4 row-cols-2 row-cols-md-4 row-cols-lg-6",children:s&&s.length>0?s.map((a,m)=>e.jsx("div",{className:"col",children:e.jsx(j,{href:`/${t(r)}/products?category=${a.slug||a.id}`,children:e.jsxs("div",{className:"category-card py-4 px-2 text-center h-100 d-flex flex-column align-items-center justify-content-center",style:{backgroundColor:"#F3F4F6",borderRadius:"10px",transition:"all 0.3s ease",cursor:"pointer",minHeight:"220px"},children:[e.jsx("div",{className:"cat-img-wrapper mb-3 d-flex align-items-center justify-content-center",style:{height:"160px",width:"100%"},children:e.jsx("img",{src:i(a.image),alt:a.name,style:{maxHeight:"100%",maxWidth:"100%",objectFit:"contain"}})}),e.jsx("h6",{className:"fw-medium text-dark mb-0",style:{fontSize:"0.9rem"},children:a.name})]})})},m)):e.jsx("div",{className:"col-12 text-center text-muted",children:"No categories found."})})]})})}function z({products:s,getImageUrl:r}){return e.jsx("section",{className:"space-top space-extra-bottom bg-white",id:"latest-products",children:e.jsxs("div",{className:"container th-container",children:[e.jsx(y,{subTitle:"New",title:"Products",align:"text-center"}),e.jsx(p,{modules:[v,w],spaceBetween:24,slidesPerView:1,autoplay:{delay:3e3,disableOnInteraction:!1},breakpoints:{575:{slidesPerView:2},992:{slidesPerView:3},1200:{slidesPerView:4}},className:"latest-products-slider",style:{paddingBottom:"40px"},children:s&&s.length>0?s.map((t,i)=>e.jsx(x,{children:e.jsxs("div",{className:"product-card-simple latest-product-height position-relative overflow-hidden w-100",style:{borderRadius:"30px",boxShadow:"0 10px 30px rgba(0,0,0,0.3)",cursor:"pointer"},children:[e.jsx("img",{src:r(t.image_path),alt:t.name,className:"w-100 h-100 object-fit-cover",style:{transition:"transform 0.5s ease"}}),e.jsxs("div",{className:"product-content position-absolute bottom-0 start-0 w-100 p-4 d-flex flex-column justify-content-end",style:{height:"100%",background:"linear-gradient(to top, #111 0%, rgba(17,17,17,0.8) 30%, transparent 100%)"},children:[e.jsx("h5",{className:"text-white mb-1 fw-bold",children:t.name}),t.category&&e.jsx("p",{className:"text-white-50 fs-xs mb-0 fw-medium",children:t.category})]})]})},i)):e.jsx("div",{className:"text-muted",children:"No latest products found."})})]})})}const W=({brand:s,products:r,categories:t,pageSite:i,relatedServices:a})=>{const[m,n]=h.useState(null),[l,c]=h.useState(null);h.useEffect(()=>{document.querySelectorAll(".scroll-text-ani").forEach(f=>{S.to(f,{backgroundImage:"linear-gradient(to right, #0B1422 100%, #D5D7DA 100%)",ease:"none",scrollTrigger:{trigger:f,start:"top bottom",end:"top center",scrub:!0}})})},[]);const d=o=>o?o.startsWith("http")||o.startsWith("/assets")?o:`/storage/${o}`:"",g=o=>o.slug||o.name.toLowerCase(),u=i||{hero_styles:{background_color:"#444A572C",background_image:"https://activ.co.id/wp-content/uploads/2023/11/Group-97-1.png?id=5807"}},b=[{title:"Purnomo",category:"Logitech",image:"/assets/img/project/project_3_9_.jpg"},{title:"Project Alpha",category:"Yealink",image:"/assets/img/project/project_3_9_.jpg"},{title:"Meeting Room B",category:"Logitech",image:"/assets/img/project/project_3_9_.jpg"},{title:"Conference Hall",category:"Jabra",image:"/assets/img/project/project_3_9_.jpg"},{title:"Huddle Space",category:"Maxhub",image:"/assets/img/project/project_3_9_.jpg"},{title:"Executive Office",category:"Zoom",image:"/assets/img/project/project_3_9_.jpg"}];return e.jsxs(_,{children:[e.jsx(N,{title:s.name}),e.jsx(C,{brand:s,pageData:u,relatedServices:a,getImageUrl:d,setLightboxImage:c}),e.jsx(B,{relatedServices:a,getImageUrl:d}),e.jsx(E,{categories:t,brand:s,getBrandSlug:g,getImageUrl:d}),e.jsx(z,{products:r,getImageUrl:d}),e.jsx(P,{staticShowcase:b}),e.jsx("style",{children:`
                .hover-opacity:hover {
                    opacity: 0.7;
                }
                .hover-white:hover {
                    background-color: rgba(255, 255, 255, 0.1) !important;
                    border-color: #ffffff !important;
                    color: #ffffff !important;
                }
                .hover-dark:hover {
                    background-color: rgba(0, 0, 0, 0.1) !important;
                    border-color: #000000 !important;
                    color: #000000 !important;
                }
                .room-card:hover img {
                    transform: scale(1.1);
                }
                .room-card {
                    overflow: hidden;
                    backface-visibility: hidden;
                }
                .category-card:hover {
                    background-color: #E5E7EB !important;
                    transform: translateY(-5px);
                }
                .product-card-simple:hover img {
                    transform: scale(1.05) !important;
                }
                .room-img-height {
                    height: 500px;
                }
                .room-btn {
                    padding: 10px 25px;
                }
                .latest-product-height {
                    height: 400px;
                }
                @media (max-width: 576px) {
                    .room-img-height {
                        height: 380px;
                    }
                    .room-btn {
                        padding: 8px 15px !important;
                        font-size: 14px;
                        width: 100%;
                        justify-content: center;
                    }
                    .latest-product-height {
                        height: 350px;
                    }
                }
                .swiper-pagination-progressbar-fill {
                    background-color: #ffffff !important;
                }
                .swiper-pagination-lock {
                    display: none !important;
                }
                .btn-ghost-green {
                    background-color: transparent !important;
                    border: 1px solid #ffffff !important;
                    color: #ffffff !important;
                    transition: all 0.3s ease;
                    position: relative;
                    z-index: 1;
                }
                .btn-ghost-green:hover {
                    background-color: #28a745 !important;
                    border-color: #28a745 !important;
                    color: #ffffff !important;
                }
                .btn-ghost-green::before,
                .btn-ghost-green::after {
                    display: none !important;
                }
                .scroll-text-ani {
                    background-clip: text;
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
             `}),l&&e.jsxs("div",{className:"position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center",style:{zIndex:9999,backgroundColor:"rgba(0,0,0,0.9)"},onClick:()=>c(null),children:[e.jsx("button",{className:"btn-close btn-close-white position-absolute top-0 end-0 m-4",onClick:()=>c(null),"aria-label":"Close",style:{width:"2rem",height:"2rem",backgroundSize:"contain"}}),e.jsx("img",{src:l,alt:"Award Full View",className:"img-fluid",style:{maxHeight:"90vh",maxWidth:"90vw",objectFit:"contain",animation:"zoomIn 0.3s ease-out"},onClick:o=>o.stopPropagation()}),e.jsx("style",{children:`
                        @keyframes zoomIn {
                            from { transform: scale(0.9); opacity: 0; }
                            to { transform: scale(1); opacity: 1; }
                        }
                    `})]})]})};export{W as default};
