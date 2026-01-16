<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Solid - Tailwind CSS Saas Template</title>
    <link rel="icon" href="favicon.ico">
    <link href="css/main.css" rel="stylesheet">
</head>

<body
    x-data="{ page: 'home', 'loaded': true, 'darkMode': true, 'stickyMenu': false, 'navigationOpen': false, 'scrollTop': false }"
    x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'a tf': darkMode === true}">


<!-- ===== Bg Lines Start ===== -->
<div class="f i l -ud-z-10 qb hc rc ee ie">
    <span class="hd hc dg wl qb ud"></span>
    <span class="hd hc dg wl qb vd"></span>
    <span class="hd hc dg wl qb wd"></span>
</div>

<!-- ===== Bg Lines End ===== -->

<!-- ===== Header Start ===== -->
<header class="f l i rc y ph" :class="{ 'uf tl xj qh zj fk' : stickyMenu }"
        @scroll.window="stickyMenu = (window.pageYOffset > 20) ? true : false">
    <div class="ba md _g nn 2xl:ud-px-0 do ee he h">
        <div class="rc ko/4 qb ee he">
            <a href="index.html">
                <img class="nl" src="images/logoM.svg" alt="Logo Light" />
                <img class="ub ml" src="images/logoM.svg" alt="Logo Dark" />
            </a>

            <!-- Hamburger Toggle BTN -->
            <button class="eo ob" @click="navigationOpen = !navigationOpen">
                    <span class="ob h yd dd gc">
                        <span class="ob g rc hc">
                            <span class="ob h i l tf ul df ed ic da hk gk _j"
                                  :class="{ 'fd ak': !navigationOpen }"></span>
                            <span class="ob h i l tf ul df ed ic da hk gk bk"
                                  :class="{ 'fd pr': !navigationOpen }"></span>
                            <span class="ob h i l tf ul df ed ic da hk gk ck"
                                  :class="{ 'fd dk': !navigationOpen }"></span>
                        </span>
                        <span class="ob g rc hc td">
                            <span class="ob tf ul df hk gk ak g r i gd hc" :class="{ 'jc _j': !navigationOpen }"></span>
                            <span class="ob tf ul df hk gk pr g l top-2.5 rc ic"
                                  :class="{ 'jc ck': !navigationOpen }"></span>
                        </span>
                    </span>
            </button>
            <!-- Hamburger Toggle BTN -->
        </div>

        <div class="rc lg:w-3/4 jc fo e un do ee he" :class="{ 'd uf sl sj kc oc we _e lb ug': navigationOpen }">
            <nav>
                <ul class="qb ro be po qe dp">
                    <li><a href="" class="bl" :class="{ 'hj': page === 'home' }">Новости</a></li>
                    <li><a href="" class="bl">Документы</a></li>
                    <li><a href="" class="bl">О нас</a></li>
                    <li><a href="" class="bl">Контакты</a></li>
                </ul>
            </nav>

            <div class="qb ee se ka bo">
                <a href="{{route('welcome')}}" class="qb ee ge wf zk hk ek fj wi xe bh dh">Начать</a>
            </div>
        </div>
    </div>
</header>

<!-- ===== Header End ===== -->

<main>
    <!-- ===== Hero Start ===== -->
    <section id="#home" class="th rn dr uh er ve">
        <div class="ba md _g nn 2xl:ud-px-0">
            <div class="qb ro to gq">
                <div class="animate_left vm/2">
                    <h4 class="ej dm oi bj ra">Выведите свой менеджмент на другой уровень</h4>
                    <h1 class="ej dm ri jr cj ga">
                        Современная система управления <span
                            class="pb h ik jk kk pk nk sk im before:-ud-z-1">командами</span> и <span
                            class="pb h ik jk kk pk nk sk im before:-ud-z-1">задачами</span>


                    </h1>
                    <div class="ma">
                        <div class="qb de qe">
                            <button class="qb tf xk rl fj xe hk ek dh bh mm"><svg width="24" height="24"
                                                                                  fill="#ffffff" viewBox="0 0 24 24" transform="" id="injected-svg"
                                                                                  xmlns="http://www.w3.org/2000/svg"><!--Boxicons v3.0 https://boxicons.com | License  https://docs.boxicons.com/free-->
                                    <path
                                        d="M6.51 18.87a1 1 0 0 0 1-.01l10-6c.3-.18.49-.51.49-.86s-.18-.68-.49-.86l-10-6a.99.99 0 0 0-1.01-.01c-.31.18-.51.51-.51.87v12c0 .36.19.69.51.87ZM8 7.77 15.06 12 8 16.23z">
                                    </path>
                                </svg>Смотреть демо</button>
                        </div>
                    </div>
                </div>
                <div class="animate_right vm/2 ub co">
                    <div class="h 2xl:-ud-mr-7.5">
                        <img src="images/shape-01.png" alt="shape" class="g -ud-left-11.5 i" />
                        <img src="images/shape-02.svg" alt="shape" class="g j k" />
                        <img src="images/shape-03.svg" alt="shape" class="g -ud-right-6.5 k" />
                        <div>
                            <img class="nl qj" src="images/hero-light.png" alt="Hero" />
                            <img class="ub ml qj" src="images/hero-dark.png" alt="Hero" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===== Hero End ===== -->

    <!-- ===== Clients Start ===== -->
    <section class="vf tl hf jf rf ql fh mt-40">
        <div class="ba md _g nn 2xl:ud-px-0">
            <div class="tb ae cn le uo iq ge ee">
                <a href="#" class="animate_top ob">
                    <img class="ij yj ek dl nl" src="images/brand-light-01.svg" alt="Clients" />
                    <img class="ub jj yj ek dl ml" src="images/brand-dark-01.svg" alt="Clients" />
                </a>
                <a href="#" class="animate_top ob">
                    <img class="ij yj ek dl nl" src="images/brand-light-02.svg" alt="Clients" />
                    <img class="ub jj yj ek dl ml" src="images/brand-dark-02.svg" alt="Clients" />
                </a>
                <a href="#" class="ob animate_top">
                    <img class="ij yj ek dl nl" src="images/brand-light-03.svg" alt="Clients" />
                    <img class="ub jj yj ek dl ml" src="images/brand-dark-03.svg" alt="Clients" />
                </a>
                <a href="#" class="ob animate_top">
                    <img class="ij yj ek dl nl" src="images/brand-light-04.svg" alt="Clients" />
                    <img class="ub jj yj ek dl ml" src="images/brand-dark-04.svg" alt="Clients" />
                </a>
                <a href="#" class="ob animate_top">
                    <img class="ij yj ek dl nl" src="images/brand-light-05.svg" alt="Clients" />
                    <img class="ub jj yj ek dl ml" src="images/brand-dark-05.svg" alt="Clients" />
                </a>
                <a href="#" class="ob animate_top">
                    <img class="ij yj ek dl nl" src="images/brand-light-06.svg" alt="Clients" />
                    <img class="ub jj yj ek dl ml" src="images/brand-dark-06.svg" alt="Clients" />
                </a>
            </div>
        </div>
    </section>
    <!-- ===== Clients End ===== -->



    <!-- ===== About Start ===== -->
    <section class="uh pp cr ve">
        <div class="ba od _g nn pq">
            <div class="qb ee te wo">
                <div class="animate_left ub sm vm/2">
                    <img src="images/about-light-01.png" alt="About" class="nl" />
                    <img src="images/about-dark-01.png" alt="About" class="ub ml" />
                </div>
                <div class="animate_right vm/2">
                    <h4 class="ej dm bj dj">
                        <a href="#" class="xf fj si sb xe gh hh wa sa">New</a> start tracking visitors
                    </h4>
                    <h2 class="h cj ej dm ri jr xa">
                        Know More About Our
                        <span class="pb h ik jk kk pk nk sk im before:-ud-z-1">Product.</span>
                    </h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ultricies lacus non fermentum
                        ultrices. Fusce consectetur le.</p>

                    <div class="ia qb ee qe">
                        <div class="tc yb af hf nf pl sl qb ee ge">
                            <p class="ej dm aj ti">01</p>
                        </div>
                        <div class="uc/4">
                            <h5 class="ej dm ti ya">Lorem ipsum dolor.</h5>
                            <p>Ut ultricies lacus non fermentum ultrices.</p>
                        </div>
                    </div>
                    <div class="ia qb ee qe">
                        <div class="tc yb af hf nf pl sl qb ee ge">
                            <p class="ej dm aj ti">02</p>
                        </div>
                        <div class="uc/4">
                            <h5 class="ej dm ti ya">Fusce consectetur le.</h5>
                            <p>consectetur adipiscing elit fermentum ultricies.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===== About End ===== -->

    <!-- ===== About Two Start ===== -->
    <section>
        <div class="ba od _g nn 2xl:ud-px-0 ve">
            <div class="qb ee te wo">
                <div class="animate_left vm/2">
                    <h4 class="ej dm bj dj">grow your business faster</h4>
                    <h2 class="h cj ej dm ri jr xa">
                        Track Your Audience
                        <span class="pb h ik jk kk pk nk tk im before:-ud-z-1">Activities</span>
                    </h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ultricies lacus non fermentum
                        ultrices. Fusce consectetur le.</p>

                    <a href="#" class="qb ee je ej dm ia">
                        Know More
                        <img class="nl" src="images/icon-arrow-light.svg" alt="Arrow" />
                        <img class="ub ml" src="images/icon-arrow-dark.svg" alt="Arrow" />
                    </a>
                </div>
                <div class="animate_right ub sm vm/2">
                    <img src="images/about-light-02.png" alt="About" class="nl" />
                    <img src="images/about-dark-02.png" alt="About" class="ub ml" />
                </div>
            </div>
        </div>
    </section>
    <!-- ===== About Two End ===== -->

    <!-- ===== Features Tab Start ===== -->
    <section class="vh uh qp h" x-data="{ featuresTab: 'tabOne' }">
        <div class="ba md _g nn 2xl:ud-px-0">
            <div class="g i -ud-z-1">
                <img class="nl" src="images/shape-dotted-light.svg" alt="Dotted Shape" />
                <img class="ub ml" src="images/shape-dotted-dark.svg" alt="Dotted Shape" />
            </div>

            <!-- Tab Menues Start -->
            <div class="animate_top hf nf pl sl sj hm uf bf qb de en fn ge yo eq _a wp">
                <div class="h yd rc wm kf wk jn nf pl qb ee pe ch qq xg rq"
                     :class="{ 'active pk ok uk ik mk kk qk rk': featuresTab === 'tabOne' }"
                     @click.prevent="featuresTab = 'tabOne'">
                    <div class="vc zb af hf nf pl sl qb ee ge">
                        <p class="ej dm bj ui">01</p>
                    </div>
                    <div class="io xm/5">
                        <h5 class="ej dm vi lr bj">Clean User Interface</h5>
                    </div>
                </div>
                <div class="h yd rc wm kf wk jn nf pl qb ee pe ch qq xg rq"
                     :class="{ 'active pk ok uk ik mk kk qk rk': featuresTab === 'tabTwo' }"
                     @click.prevent="featuresTab = 'tabTwo'">
                    <div class="vc zb af hf nf pl sl qb ee ge">
                        <p class="ej dm bj ui">02</p>
                    </div>
                    <div class="io xm/5">
                        <h5 class="ej dm vi lr bj">Cloud Data Save Options</h5>
                    </div>
                </div>
                <div class="h yd rc wm kf wk jn nf pl qb ee pe ch qq xg rq"
                     :class="{ 'active pk ok uk ik mk kk qk rk': featuresTab === 'tabThree' }"
                     @click.prevent="featuresTab = 'tabThree'">
                    <div class="vc zb af hf nf pl sl qb ee ge">
                        <p class="ej dm bj ui">03</p>
                    </div>
                    <div class="io xm/5">
                        <h5 class="ej dm vi lr bj">Management And Collaboration</h5>
                    </div>
                </div>
            </div>
            <!-- Tab Menues End -->

            <!-- Tab Content Start -->
            <div class="animate_top ba pd">
                <div x-show="featuresTab === 'tabOne'">
                    <div class="qb ee te _o">
                        <div class="vm/2">
                            <h2 class="ej dm ri mr cj fa">Solid Has Neat & Clean User Interface.</h2>
                            <p class="ga">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ultricies
                                lacus non fermentum ultrices. Fusce consectetur le.</p>
                            <p class="xc/12">Nam id eleifend dui, id iaculis purus. Etiam lobortis neque nec finibus
                                sagittis. Nulla ligula nunc egestas ut.</p>
                        </div>
                        <div class="ub sm vm/2">
                            <img src="images/features-light-01.png" alt="Features" class="nl" />
                            <img src="images/features-dark-01.png" alt="Features" class="ub ml" />
                        </div>
                    </div>
                </div>

                <div x-show="featuresTab === 'tabTwo'">
                    <div class="qb ee te _o">
                        <div class="vm/2">
                            <h2 class="ej dm ri mr cj fa">Solid Has Cloud Data Save Options.</h2>
                            <p class="ga">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ultricies
                                lacus non fermentum ultrices. Fusce consectetur le.</p>
                            <p class="xc/12">Nam id eleifend dui, id iaculis purus. Etiam lobortis neque nec finibus
                                sagittis. Nulla ligula nunc egestas ut.</p>
                        </div>
                        <div class="ub sm vm/2">
                            <img src="images/features-light-01.png" alt="Features" class="nl" />
                            <img src="images/features-dark-01.png" alt="Features" class="ub ml" />
                        </div>
                    </div>
                </div>

                <div x-show="featuresTab === 'tabThree'">
                    <div class="qb ee te _o">
                        <div class="vm/2">
                            <h2 class="ej dm ri mr cj fa">Solid Has Management And Collaboration.</h2>
                            <p class="ga">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ultricies
                                lacus non fermentum ultrices. Fusce consectetur le.</p>
                            <p class="xc/12">Nam id eleifend dui, id iaculis purus. Etiam lobortis neque nec finibus
                                sagittis. Nulla ligula nunc egestas ut.</p>
                        </div>
                        <div class="ub sm vm/2">
                            <img src="images/features-light-01.png" alt="Features" class="nl" />
                            <img src="images/features-dark-01.png" alt="Features" class="ub ml" />
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tab Content End -->
        </div>
    </section>
    <!-- ===== Features Tab End ===== -->

    <!-- ===== Funfact Start ===== -->
    <section class="zg fp _g nn 2xl:ud-px-0">
        <div class="ba md ih tq h w ye fg gg hg xl yl _l sl cm">
            <img src="images/shape-05.png" alt="Doodle" class="g k j -ud-z-1" />
            <img src="images/shape-dotted-light-02.svg" alt="Dotted" class="g i l -ud-z-1 nl" />
            <img src="images/shape-dotted-dark-02.svg" alt="Dotted" class="g i l -ud-z-1 ub ml" />

            <div class="animate_top ba mi ym/5 ho/3 zp/2 ab yn _g on">
                <h2 class="cj ej dm ri nr sa">Начните пользоваться уже сегодня</h2>
                <p class="jo/12 ba">МенеджерПлюс — это быстроразвивающийся отечественный продукт, разработанный на
                    основе передовых технологий.
                    Платфоорма предоставляет все необходимые инструменты для комфортной и продуктивной работы.
                    Мы уделяем большое внимание конфиденциальности и безопасности ваших данных.</p>
            </div>

            <div class="qb de ge te ap">
                <div class="animate_top mi">
                    <h3 class="cj ej dm ri nr cb">100+</h3>
                    <p class="oi up">Пользователей</p>
                </div>
                <div class="animate_top mi">
                    <h3 class="cj ej dm ri nr cb">4</h3>
                    <p class="oi up">Крупные компании</p>
                </div>
                <div class="animate_top mi">
                    <h3 class="cj ej dm ri nr cb">30</h3>
                    <p class="oi up">Подписок</p>
                </div>
            </div>
        </div>
    </section>
    <!-- ===== Funfact End ===== -->

    <!-- ===== Features Start ===== -->
    <section id="features" class="zg ep oq">
        <div class="ba nd _g nn pq">
            <!-- Section Title Start -->
            <div class="animate_top mi ba"
                 x-data="{ sectionTitle: ``, sectionSubtitle: `Почему выбирают нас`, sectionTitlePara: ` `}">
                <div class="eg sl ol pl pb xe oh hh sa">
                    <h4 x-text="sectionTitle" class="bj yi ej dm"></h4>
                </div>
                <h2 x-text="sectionSubtitle" class="cj ri nr ej dm ym/5 zp/2 ba sa"></h2>
                <p class="ba ym/5 mo/5 aq" x-text="sectionTitlePara"></p>

            </div>
            <!-- Section Title End -->

            <div class="tb _d bn oo le eq ua xn vp">
                <!-- Features item Start -->
                <div class="animate_top hf of rj ye ug lq yj el km pl sl">
                    <div class="qb ee ge ze wf sc xb">
                        <img src="images/icon-01.svg" alt="Icon" />
                    </div>
                    <h3 class="aj qi kr ej dm ia ga">Crafted for SaaS</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In convallis tortor.</p>
                </div>
                <!-- Features item End -->

                <!-- Features item Start -->
                <div class="animate_top hf of rj ye ug lq yj el km pl sl">
                    <div class="qb ee ge ze wf sc xb">
                        <img src="images/icon-02.svg" alt="Icon" />
                    </div>
                    <h3 class="aj qi kr ej dm ia ga">High-quality Design</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In convallis tortor.</p>
                </div>
                <!-- Features item End -->

                <!-- Features item Start -->
                <div class="animate_top hf of rj ye ug lq yj el km pl sl">
                    <div class="qb ee ge ze wf sc xb">
                        <img src="images/icon-03.svg" alt="Icon" />
                    </div>
                    <h3 class="aj qi kr ej dm ia ga">All Essential Sections</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In convallis tortor.</p>
                </div>
                <!-- Features item End -->

                <!-- Features item Start -->
                <div class="animate_top hf of rj ye ug lq yj el km pl sl">
                    <div class="qb ee ge ze wf sc xb">
                        <img src="images/icon-04.svg" alt="Icon" />
                    </div>
                    <h3 class="aj qi kr ej dm ia ga">Speed Optimized</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In convallis tortor.</p>
                </div>
                <!-- Features item End -->

                <!-- Features item Start -->
                <div class="animate_top hf of rj ye ug lq yj el km pl sl">
                    <div class="qb ee ge ze wf sc xb">
                        <img src="images/icon-05.svg" alt="Icon" />
                    </div>
                    <h3 class="aj qi kr ej dm ia ga">Fully Customizable</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In convallis tortor.</p>
                </div>
                <!-- Features item End -->

                <!-- Features item Start -->
                <div class="animate_top hf of rj ye ug lq yj el km pl sl">
                    <div class="qb ee ge ze wf sc xb">
                        <img src="images/icon-06.svg" alt="Icon" />
                    </div>
                    <h3 class="aj qi kr ej dm ia ga">Regular Updates</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In convallis tortor.</p>
                </div>
                <!-- Features item End -->
            </div>
        </div>
    </section>
    <!-- ===== Features End ===== -->

    <!-- ===== CTA Start ===== -->
    <section class="zg ep oq _g nn 2xl:ud-px-0 ve">
        <div class="ba md dh pn vq kh wq ye fg gg hg xl yl _l sl cm">
            <div class="qb de en fn gn te hn">
                <div class="animate_left zm ko/2">
                    <h2 class="ej dm ri or cj sa xc/12">Join With Us Today & Incrud-ease Your Productivity</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In convallis tortor eros. Donec
                        vitae tortor lacus. Phasellus aliquam ante in maximus.</p>
                </div>
                <div class="animate_right lo">
                    <div class="qb ee fe cq">
                        <img src="images/shape-06.png" alt="Saly" class="ub yp" />
                        <a href="signup.html" class="sb ee je bj fj em tf ul xe yg xg _k mm">
                            Sign up free
                            <img src="images/icon-arrow-dark.svg" alt="Arrow" class="nl" />
                            <img src="images/icon-arrow-light.svg" alt="Arrow" class="ub ml" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===== CTA End ===== -->

    <!-- ===== FAQ Start ===== -->
    <section class="uh pp cr ve">
        <div class="ba od _g nn pq h">
            <div class="g -ud-bottom-7.5 -ud-z-1">
                <img src="images/shape-dotted-light.svg" alt="Dotted" class="nl" />
                <img src="images/shape-dotted-light.svg" alt="Dotted" class="ub ml" />
            </div>
            <div class="qb de en fn te gq">
                <div class="animate_left _m/5 ko/2">
                    <h4 class="ej dm bj dj">OUR FAQS</h4>
                    <h2 class="h cj ej dm ri jr xa">
                        Frequently Asked
                        <span class="pb h ik jk kk pk nk tk im before:-ud-z-1">Questions</span>
                    </h2>

                    <a href="#" class="qb ee je ej dm eb">
                        Know More
                        <img class="nl" src="images/icon-arrow-light.svg" alt="Arrow" />
                        <img class="ub ml" src="images/icon-arrow-dark.svg" alt="Arrow" />
                    </a>
                </div>

                <div class="animate_right xm/5 ko/2">
                    <div class="uf sl ol pl mj ye" x-data="{selected:0}">
                        <div class="qb be kf nf pl">
                            <h4 @click="selected !== 0 ? selected = 0 : selected = null"
                                class="yd qb he ee bj ui ej dm mh hp xg ip">
                                Lorem ipsum dolor sit amet, consectetur.
                                <img src="images/icon-plus-light.svg" alt="plus" class="nl"
                                     :class="selected == 0 ? 'ub' : 'ob'" />
                                <img src="images/icon-minus-light.svg" alt="minus" class="nl"
                                     :class="selected == 0 ? 'ob' : 'ub'" />
                                <img src="images/icon-plus-dark.svg" alt="plus" class="ub ml"
                                     :class="selected == 0 ? 'nl' : 'ml'" />
                                <img src="images/icon-minus-dark.svg" alt="minus" class="ub ml"
                                     :class="selected == 0 ? 'ml' : 'nl'" />
                            </h4>
                            <p x-show="selected == 0" class="mh hp xg ip lf nf pl">
                                We provide digital experience services to startups and small businesses. We help our
                                clients succeed by creating brand identities, digital experiences.
                            </p>
                        </div>
                        <div class="qb be kf nf pl">
                            <h4 @click="selected !== 1 ? selected = 1 : selected = null"
                                class="yd qb he ee bj ui ej dm mh hp xg ip">
                                Lorem ipsum dolor sit amet, consectetur.
                                <img src="images/icon-plus-light.svg" alt="plus" class="nl"
                                     :class="selected == 1 ? 'ub' : 'ob'" />
                                <img src="images/icon-minus-light.svg" alt="minus" class="nl"
                                     :class="selected == 1 ? 'ob' : 'ub'" />
                                <img src="images/icon-plus-dark.svg" alt="plus" class="ub ml"
                                     :class="selected == 1 ? 'nl' : 'ml'" />
                                <img src="images/icon-minus-dark.svg" alt="minus" class="ub ml"
                                     :class="selected == 1 ? 'ml' : 'nl'" />
                            </h4>
                            <p x-show="selected == 1" class="mh hp xg ip lf nf pl">
                                We provide digital experience services to startups and small businesses. We help our
                                clients succeed by creating brand identities, digital experiences.
                            </p>
                        </div>
                        <div class="qb be">
                            <h4 @click="selected !== 2 ? selected = 2 : selected = null"
                                :class="{'yd qb he ee bj ui ej dm mh hp xg ip': true, 'ef': selected != 2}">
                                Lorem ipsum dolor sit amet, consectetur.
                                <img src="images/icon-plus-light.svg" alt="plus" class="nl"
                                     :class="selected == 2 ? 'ub' : 'ob'" />
                                <img src="images/icon-minus-light.svg" alt="minus" class="nl"
                                     :class="selected == 2 ? 'ob' : 'ub'" />
                                <img src="images/icon-plus-dark.svg" alt="plus" class="ub ml"
                                     :class="selected == 2 ? 'nl' : 'ml'" />
                                <img src="images/icon-minus-dark.svg" alt="minus" class="ub ml"
                                     :class="selected == 2 ? 'ml' : 'nl'" />
                            </h4>
                            <p x-show="selected == 2" :class="{'mh hp xg ip lf nf pl': true, 'ef': selected == 2}">
                                We provide digital experience services to startups and small businesses. We help our
                                clients succeed by creating brand identities, digital experiences.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===== FAQ End ===== -->

    <!-- ===== Testimonials Start ===== -->
    <section>
        <div class="ba nd _g nn pq">
            <!-- Section Title Start -->
            <div class="animate_top mi ba"
                 x-data="{ sectionTitle: `TESTIMONIALS`, sectionSubtitle: `Client’s Testimonials`, sectionTitlePara: `Lorem ipsum dolor sit amet, consectetur adipiscing elit. In convallis tortor eros. Donec vitae tortor lacus. Phasellus aliquam ante in maximus.`}">
                <div class="eg sl ol pl pb xe oh hh sa">
                    <h4 x-text="sectionTitle" class="bj yi ej dm"></h4>
                </div>
                <h2 x-text="sectionSubtitle" class="cj ri nr ej dm ym/5 zp/2 ba sa"></h2>
                <p class="ba ym/5 mo/5 aq" x-text="sectionTitlePara"></p>

            </div>
            <!-- Section Title End -->
        </div>

        <div class="animate_top ba od _g nn pq ha vp">
            <!-- Slider main container -->
            <div class="swiper testimonial-01 xh">
                <!-- Additional required wrapper -->
                <div class="swiper-wrapper">
                    <!-- Slides -->
                    <div class="swiper-slide">
                        <div class="uf ye uj gm sl ol pl tg zh">
                            <div class="qb he kf nf pl ai ea">
                                <div>
                                    <h4 class="ej dm ui fb">Devid Smith</h4>
                                    <p>Founter @democompany</p>
                                </div>
                                <img class="" src="images/user-01.png" alt="User" />
                            </div>

                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris hendrerit, ligula sit
                                amet cursus tincidunt, lorem sem elementum nisi, convallis fringilla ante nibh non
                                urna.</p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="uf ye uj gm sl ol pl tg zh">
                            <div class="qb he kf nf pl ai ea">
                                <div>
                                    <h4 class="ej dm ui fb">Jhon Abraham</h4>
                                    <p>Founter @democompany</p>
                                </div>
                                <img class="" src="images/user-02.png" alt="User" />
                            </div>

                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris hendrerit, ligula sit
                                amet cursus tincidunt, lorem sem elementum nisi, convallis fringilla ante nibh non
                                urna.</p>
                        </div>
                    </div>
                </div>
                <!-- If we need pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
    <!-- ===== Testimonials End ===== -->

    <!-- ===== Pricing Table Start ===== -->
    <section class="bi sp fr uh pp cr">
        <div class="ba nd _g nn pq">
            <!-- Section Title Start -->
            <div class="animate_top mi ba" x-data="{ sectionTitle: ``, sectionSubtitle: `Тарифы`}">
                <div class="eg sl ol pl pb xe oh hh sa">
                    <h4 x-text="sectionTitle" class="bj yi ej dm"></h4>
                </div>
                <h2 x-text="sectionSubtitle" class="cj ri nr ej dm ym/5 zp/2 ba sa"></h2>
                <p class="ba ym/5 mo/5 aq" x-text="sectionTitlePara"></p>

            </div>
            <!-- Section Title End -->
        </div>

        <div class="ba qd _g nn pq h ha vp">
            <div class="g -ud-bottom-15 -ud-z-1">
                <img src="images/shape-dotted-light.svg" alt="Dotted" class="nl" />
            </div>
            <div class="qb de qo ge le eq">
                <!-- Pricing Item -->
                <div class="animate_top an ko/3 b h uf sl ye vj gm hf nf pl ug lq">
                    <h3 class="ej dm cj ri nr ea">
                        0₽<span class="wi gj fm">/мес</span>
                    </h3>
                    <h4 class="ej dm bj xi cb">Базовый</h4>
                    <p>Lorem ipsum dolor sit amet, consec adipisicing elit.</p>

                    <div class="lf nf pl hb ci di">
                        <ul>
                            <li class="ej fm sa vk">300 GB Storage</li>
                            <li class="ej fm sa vk">Unlimited Photos and Videos</li>
                            <li class="ej fm sa vk opacity-40">Exclusive Support</li>
                            <li class="ej fm sa vk kj">Custom Branding Strategy</li>
                        </ul>
                    </div>

                    <a href="#" class="sb ee je hj dm om bj yj ek">
                        Get the Plan
                        <svg class="lg bm nm yj ek" width="14" height="14" viewBox="0 0 14 14" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10.4767 6.17348L6.00668 1.70348L7.18501 0.525146L13.6667 7.00681L7.18501 13.4885L6.00668 12.3101L10.4767 7.84015H0.333344V6.17348H10.4767Z"
                                fill="" />
                        </svg>
                    </a>
                </div>

                <!-- Pricing Item -->
                <div class="animate_top an ko/3 b h uf sl ye vj gm hf nf pl ug lq">
                    <div class="g m -ud-right-3.5 -ud-rotate-90 ff gf wf bj fj si dj oh hh">
                        Популярный
                    </div>

                    <h3 class="ej dm cj ri nr ea">
                        350₽ <span class="wi gj fm">/month</span>
                    </h3>
                    <h4 class="ej dm bj xi cb">Оптимальный</h4>
                    <p>Lorem ipsum dolor sit amet, consec adipisicing elit.</p>

                    <div class="lf nf pl hb ci di">
                        <ul>
                            <li class="ej fm sa vk">300 GB Storage</li>
                            <li class="ej fm sa vk">Unlimited Photos and Videos</li>
                            <li class="ej fm sa vk">Exclusive Support</li>
                            <li class="ej fm sa vk kj">Custom Branding Strategy</li>
                        </ul>
                    </div>

                    <a href="#" class="sb ee je hj dm om bj yj ek">
                        Get the Plan
                        <svg class="lg bm nm yj ek" width="14" height="14" viewBox="0 0 14 14" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10.4767 6.17348L6.00668 1.70348L7.18501 0.525146L13.6667 7.00681L7.18501 13.4885L6.00668 12.3101L10.4767 7.84015H0.333344V6.17348H10.4767Z"
                                fill="" />
                        </svg>
                    </a>
                </div>

                <!-- Pricing Item -->
                <div class="animate_top an ko/3 b h uf sl ye vj gm hf nf pl ug lq">
                    <h3 class="ej dm cj ri nr ea">
                        ₽<span class="wi gj fm">/month</span>
                    </h3>
                    <h4 class="ej dm bj xi cb">Премиум</h4>
                    <p>Lorem ipsum dolor sit amet, consec adipisicing elit.</p>

                    <div class="lf nf pl hb ci di">
                        <ul>
                            <li class="ej fm sa vk">300 GB Storage</li>
                            <li class="ej fm sa vk">Unlimited Photos and Videos</li>
                            <li class="ej fm sa vk">Exclusive Support</li>
                            <li class="ej fm sa vk">Custom Branding Strategy</li>
                        </ul>
                    </div>

                    <a href="#" class="sb ee je hj dm om bj yj ek">
                        Get the Plan
                        <svg class="lg bm nm yj ek" width="14" height="14" viewBox="0 0 14 14" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10.4767 6.17348L6.00668 1.70348L7.18501 0.525146L13.6667 7.00681L7.18501 13.4885L6.00668 12.3101L10.4767 7.84015H0.333344V6.17348H10.4767Z"
                                fill="" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <!-- ===== Pricing Table End ===== -->

    <!-- ===== Contact Start ===== -->
    <section id="support" class="_g nn 2xl:ud-px-0">
        <div class="ba md h fi tp gr dh kp yq ve">
            <div class="g -ud-z-1 ye l i rc dc/3 fg gg hg xl zl am"></div>
            <div class="g -ud-z-1 o l rc ec/3">
                <img src="images/shape-dotted-light.svg" alt="Dotted" class="nl" />
                <img src="images/shape-dotted-dark.svg" alt="Dotted" class="ub ml" />
            </div>

            <div class="qb de en ce dn te jq gn">
                <div class="animate_top rc xm/5 mo/4 mj ye uf tl ol pl ug nq">
                    <h2 class="ej dm ri mr aj _a">Send a message</h2>

                    <form action="https://formbold.com/s/unique_form_id" method="POST">
                        <div class="qb be po so le bp ea">
                            <input type="text" placeholder="Full name"
                                   class="rc ko/2 bg kf nf pl jl gl qm il rm gi" />

                            <input type="email" placeholder="Email address"
                                   class="rc ko/2 bg kf nf pl jl gl qm il rm gi" />
                        </div>

                        <div class="qb be po so le bp ab">
                            <input type="text" placeholder="Subject"
                                   class="rc ko/2 bg kf nf pl jl gl qm il rm gi" />

                            <input type="text" placeholder="Phone number"
                                   class="rc ko/2 bg kf nf pl jl gl qm il rm gi" />
                        </div>

                        <div class="qb ib">
                                <textarea placeholder="Message" rows="4"
                                          class="rc bg kf nf pl jl gl qm il rm"></textarea>
                        </div>

                        <div class="qb de cq re kq">
                            <div>
                                <label for="supportCheckbox" class="rd qb yd zd bl">
                                    <div class="h">
                                        <input type="checkbox" id="supportCheckbox" class="c" />
                                        <div class="box wa kb qb fc bd ee ge cf hf nf pl">
                                                <span class="lj">
                                                    <svg width="11" height="8" viewBox="0 0 11 8" fill="none"
                                                         class="og">
                                                        <path
                                                            d="M10.0915 0.951972L10.0867 0.946075L10.0813 0.940568C9.90076 0.753564 9.61034 0.753146 9.42927 0.939309L4.16201 6.22962L1.58507 3.63469C1.40401 3.44841 1.11351 3.44879 0.932892 3.63584C0.755703 3.81933 0.755703 4.10875 0.932892 4.29224L0.932878 4.29225L0.934851 4.29424L3.58046 6.95832C3.73676 7.11955 3.94983 7.2 4.1473 7.2C4.36196 7.2 4.55963 7.11773 4.71406 6.9584L10.0468 1.60234C10.2436 1.4199 10.2421 1.1339 10.0915 0.951972ZM4.2327 6.30081L4.2317 6.2998C4.23206 6.30015 4.23237 6.30049 4.23269 6.30082L4.2327 6.30081Z"
                                                            stroke-width="0.4"></path>
                                                    </svg>
                                                </span>
                                        </div>
                                    </div>
                                    By clicking Checkbox, you agree to use our “Form” terms And consent cookie usage
                                    in browser.
                                </label>
                            </div>

                            <button class="sb ee je tf xk hk ek rl bj fj xe xg yg">
                                Send Message
                                <svg class="ig" width="14" height="14" viewBox="0 0 14 14" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.4767 6.16664L6.00668 1.69664L7.18501 0.518311L13.6667 6.99998L7.18501 13.4816L6.00668 12.3033L10.4767 7.83331H0.333344V6.16664H10.4767Z"
                                        fill="" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="animate_top rc _m/5 no ln hr">
                    <h2 class="ej dm ri mr aj ab">Find us</h2>

                    <div class="fa 5">
                        <h4 class="bj ej dm ui sa">Our Loaction</h4>
                        <p>290 Maryam Springs 260, Courbevoie, Paris, France</p>
                    </div>
                    <div class="fa 5">
                        <h4 class="bj ej dm ui sa">Email Address</h4>
                        <p><a href="#"><span class="__cf_email__"
                                             data-cfemail="7d0412080f101c14113d1912101c1413131c1018531e1210">[email&#160;protected]</span></a>
                        </p>
                    </div>
                    <div>
                        <h4 class="bj ej dm ui sa">Phone Number</h4>
                        <p><a href="#">+009 42334 6343 843</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===== Contact End ===== -->

    <!-- ===== Blog Start ===== -->
    <section class="zg ep oq">
        <div class="ba nd _g nn pq">
            <!-- Section Title Start -->
            <div class="animate_top mi ba"
                 x-data="{ sectionTitle: `NEWS & BLOGS`, sectionSubtitle: `Latest News & Blogs`, sectionTitlePara: `Lorem ipsum dolor sit amet, consectetur adipiscing elit. In convallis tortor eros. Donec vitae tortor lacus. Phasellus aliquam ante in maximus.`}">
                <div class="eg sl ol pl pb xe oh hh sa">
                    <h4 x-text="sectionTitle" class="bj yi ej dm"></h4>
                </div>
                <h2 x-text="sectionSubtitle" class="cj ri nr ej dm ym/5 zp/2 ba sa"></h2>
                <p class="ba ym/5 mo/5 aq" x-text="sectionTitlePara"></p>

            </div>
            <!-- Section Title End -->
        </div>

        <div class="ba ld _g nn pq ha vp">
            <div class="tb _d bn oo le dq">
                <!-- Blog Item -->
                <div class="animate_top uf sl ye mj pg sh">
                    <a href="#" class="ob">
                        <img src="images/blog-01.png" alt="Blog" />
                    </a>

                    <div class="_g">
                        <h4 class="bj oi ir ej bl dm ia ja">
                            <a href="blog-single.html">Free advertising for your online business</a>
                        </h4>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit convallis tortor.</p>
                    </div>
                </div>

                <!-- Blog Item -->
                <div class="animate_top uf sl ye mj pg sh">
                    <a href="#" class="ob">
                        <img src="images/blog-02.png" alt="Blog" />
                    </a>

                    <div class="_g">
                        <h4 class="bj oi ir ej bl dm ia ja">
                            <a href="bog-single.html">9 simple ways to improve your design skills</a>
                        </h4>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit convallis tortor.</p>
                    </div>
                </div>

                <!-- Blog Item -->
                <div class="animate_top uf sl ye mj pg sh">
                    <a href="#" class="ob">
                        <img src="images/blog-03.png" alt="Blog" />
                    </a>

                    <div class="_g">
                        <h4 class="bj oi ir ej bl dm ia ja">
                            <a href="blog-single.html">Tips to quickly improve your coding speed.</a>
                        </h4>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit convallis tortor.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===== Blog End ===== -->
</main>
<!-- ===== Footer Start ===== -->
<footer class="uf sl lf nf pl">
    <div class="ba md _g nn 2xl:ud-px-0">
        <!-- Footer Top -->
        <div class="zg ep">
            <div class="qb de so te cp">
                <div class="animate_top yc/2 ko/4">
                    <a href="index.html">
                        <img src="images/logo-light.svg" alt="Logo" class="nl" />
                        <img src="images/logo-dark.svg" alt="Logo" class="ub ml" />
                    </a>

                    <p class="ta na">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>

                    <p class="dj tracking-[5px] yi fb">contact</p>
                    <a href="#" class="ej dm bj zi"><span class="__cf_email__"
                                                          data-cfemail="c9a1aca5a5a689baa6a5a0ade7aaa6a4">[email&#160;protected]</span></a>
                </div>

                <div class="rc ho/3 _p/12 qb be dn gn te hn">
                    <div class="animate_top">
                        <h4 class="bj ej dm _i pa">Quick Links</h4>

                        <ul>
                            <li><a href="#" class="pb bl la">Home</a></li>
                            <li><a href="#" class="pb bl la">Product</a></li>
                            <li><a href="#" class="pb bl la">Careers</a></li>
                            <li><a href="#" class="pb bl la">Pricing</a></li>
                        </ul>
                    </div>

                    <div class="animate_top">
                        <h4 class="bj ej dm _i pa">Support</h4>

                        <ul>
                            <li><a href="#" class="pb bl la">Company</a></li>
                            <li><a href="#" class="pb bl la">Press media</a></li>
                            <li><a href="#" class="pb bl la">Our Blog</a></li>
                            <li><a href="#" class="pb bl la">Contact Us</a></li>
                        </ul>
                    </div>

                    <div class="animate_top">
                        <h4 class="bj ej dm _i pa">Newsletter</h4>
                        <p class="sa cd">Subscribe to receive future updates</p>

                        <form action="#">
                            <div class="h">
                                <input type="text" placeholder="Email address"
                                       class="rc tl hf nf pl wj gm xe hl fl pm yg xg" />

                                <button class="g j pg">
                                    <svg class="mg bm al" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_48_1487)">
                                            <path
                                                d="M3.1175 1.17318L18.5025 9.63484C18.5678 9.67081 18.6223 9.72365 18.6602 9.78786C18.6982 9.85206 18.7182 9.92527 18.7182 9.99984C18.7182 10.0744 18.6982 10.1476 18.6602 10.2118C18.6223 10.276 18.5678 10.3289 18.5025 10.3648L3.1175 18.8265C3.05406 18.8614 2.98262 18.8792 2.91023 18.8781C2.83783 18.8769 2.76698 18.857 2.70465 18.8201C2.64232 18.7833 2.59066 18.7308 2.55478 18.6679C2.51889 18.6051 2.50001 18.5339 2.5 18.4615V1.53818C2.50001 1.46577 2.51889 1.39462 2.55478 1.33174C2.59066 1.26885 2.64232 1.2164 2.70465 1.17956C2.76698 1.14272 2.83783 1.12275 2.91023 1.12163C2.98262 1.12051 3.05406 1.13828 3.1175 1.17318ZM4.16667 10.8332V16.3473L15.7083 9.99984L4.16667 3.65234V9.16651H8.33333V10.8332H4.16667Z"
                                                fill="" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_48_1487">
                                                <rect width="20" height="20" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer Top -->

        <!-- Footer Bottom -->
        <div class="lf nf pl qb de be po ee ge so qe cp ph">
            <div class="animate_top">
                <ul class="qb ee te">
                    <li><a href="#" class="bl">English</a></li>
                    <li><a href="#" class="bl">Privacy Policy</a></li>
                    <li><a href="#" class="bl">Support</a></li>
                </ul>
            </div>

            <div class="animate_top">
                <p>&copy; 2025 Solid. All rights reserved</p>
            </div>

            <div class="animate_top">
                <ul class="qb ee qe">
                    <li>
                        <a href="#">
                            <svg class="kg al yj ek" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_48_1499)">
                                    <path
                                        d="M14 13.5H16.5L17.5 9.5H14V7.5C14 6.47 14 5.5 16 5.5H17.5V2.14C17.174 2.097 15.943 2 14.643 2C11.928 2 10 3.657 10 6.7V9.5H7V13.5H10V22H14V13.5Z"
                                        fill="" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_48_1499">
                                        <rect width="24" height="24" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <svg class="kg al yj ek" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_48_1502)">
                                    <path
                                        d="M22.162 5.65593C21.3985 5.99362 20.589 6.2154 19.76 6.31393C20.6337 5.79136 21.2877 4.96894 21.6 3.99993C20.78 4.48793 19.881 4.82993 18.944 5.01493C18.3146 4.34151 17.4803 3.89489 16.5709 3.74451C15.6615 3.59413 14.7279 3.74842 13.9153 4.18338C13.1026 4.61834 12.4564 5.30961 12.0771 6.14972C11.6978 6.98983 11.6067 7.93171 11.818 8.82893C10.1551 8.74558 8.52832 8.31345 7.04328 7.56059C5.55823 6.80773 4.24812 5.75098 3.19799 4.45893C2.82628 5.09738 2.63095 5.82315 2.63199 6.56193C2.63199 8.01193 3.36999 9.29293 4.49199 10.0429C3.828 10.022 3.17862 9.84271 2.59799 9.51993V9.57193C2.59819 10.5376 2.93236 11.4735 3.54384 12.221C4.15532 12.9684 5.00647 13.4814 5.95299 13.6729C5.33661 13.84 4.6903 13.8646 4.06299 13.7449C4.32986 14.5762 4.85 15.3031 5.55058 15.824C6.25117 16.345 7.09712 16.6337 7.96999 16.6499C7.10247 17.3313 6.10917 17.8349 5.04687 18.1321C3.98458 18.4293 2.87412 18.5142 1.77899 18.3819C3.69069 19.6114 5.91609 20.2641 8.18899 20.2619C15.882 20.2619 20.089 13.8889 20.089 8.36193C20.089 8.18193 20.084 7.99993 20.076 7.82193C20.8949 7.2301 21.6016 6.49695 22.163 5.65693L22.162 5.65593Z"
                                        fill="" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_48_1502">
                                        <rect width="24" height="24" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <svg class="kg al yj ek" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_48_1505)">
                                    <path
                                        d="M6.94 5.00002C6.93974 5.53046 6.72877 6.03906 6.35351 6.41394C5.97825 6.78883 5.46944 6.99929 4.939 6.99902C4.40857 6.99876 3.89997 6.78779 3.52508 6.41253C3.1502 6.03727 2.93974 5.52846 2.94 4.99802C2.94027 4.46759 3.15124 3.95899 3.5265 3.5841C3.90176 3.20922 4.41057 2.99876 4.941 2.99902C5.47144 2.99929 5.98004 3.21026 6.35492 3.58552C6.72981 3.96078 6.94027 4.46959 6.94 5.00002ZM7 8.48002H3V21H7V8.48002ZM13.32 8.48002H9.34V21H13.28V14.43C13.28 10.77 18.05 10.43 18.05 14.43V21H22V13.07C22 6.90002 14.94 7.13002 13.28 10.16L13.32 8.48002Z"
                                        fill="" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_48_1505">
                                        <rect width="24" height="24" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <svg class="kg al yj ek" width="24" height="24" viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_48_1508)">
                                    <path
                                        d="M7.443 5.3501C8.082 5.3501 8.673 5.4001 9.213 5.5481C9.70301 5.63814 10.1708 5.82293 10.59 6.0921C10.984 6.3391 11.279 6.6861 11.475 7.1311C11.672 7.5761 11.77 8.1211 11.77 8.7141C11.77 9.4071 11.623 10.0001 11.279 10.4451C10.984 10.8911 10.492 11.2861 9.902 11.5831C10.738 11.8311 11.377 12.2761 11.77 12.8691C12.164 13.4631 12.41 14.2051 12.41 15.0461C12.41 15.7391 12.262 16.3321 12.016 16.8271C11.77 17.3221 11.377 17.7671 10.934 18.0641C10.4528 18.3825 9.92084 18.6165 9.361 18.7561C8.771 18.9051 8.181 19.0041 7.591 19.0041H1V5.3501H7.443ZM7.049 10.8901C7.59 10.8901 8.033 10.7421 8.377 10.4951C8.721 10.2481 8.869 9.8021 8.869 9.2581C8.869 8.9611 8.819 8.6641 8.721 8.4671C8.623 8.2691 8.475 8.1201 8.279 7.9721C8.082 7.8731 7.885 7.7741 7.639 7.7251C7.393 7.6751 7.148 7.6751 6.852 7.6751H4V10.8911H7.05L7.049 10.8901ZM7.197 16.7281C7.492 16.7281 7.787 16.6781 8.033 16.6291C8.28138 16.5819 8.51628 16.4805 8.721 16.3321C8.92139 16.1873 9.08903 16.002 9.213 15.7881C9.311 15.5411 9.41 15.2441 9.41 14.8981C9.41 14.2051 9.213 13.7101 8.82 13.3641C8.426 13.0671 7.885 12.9191 7.246 12.9191H4V16.7291H7.197V16.7281ZM16.689 16.6781C17.082 17.0741 17.672 17.2721 18.459 17.2721C19 17.2721 19.492 17.1241 19.885 16.8771C20.279 16.5801 20.525 16.2831 20.623 15.9861H23.033C22.639 17.1731 22.049 18.0141 21.263 18.5581C20.475 19.0531 19.541 19.3501 18.41 19.3501C17.6864 19.3523 16.9688 19.2179 16.295 18.9541C15.6887 18.7266 15.148 18.3529 14.721 17.8661C14.2643 17.4107 13.9267 16.8498 13.738 16.2331C13.492 15.5901 13.393 14.8981 13.393 14.1061C13.393 13.3641 13.492 12.6721 13.738 12.0281C13.9745 11.4082 14.3245 10.8378 14.77 10.3461C15.213 9.9011 15.754 9.5061 16.344 9.2581C17.0007 8.99416 17.7022 8.85969 18.41 8.8621C19.246 8.8621 19.984 9.0111 20.623 9.3571C21.263 9.7031 21.754 10.0991 22.148 10.6931C22.5499 11.2636 22.8494 11.8998 23.033 12.5731C23.131 13.2651 23.18 13.9581 23.131 14.7491H16C16 15.5411 16.295 16.2831 16.689 16.6791V16.6781ZM19.787 11.4841C19.443 11.1381 18.902 10.9401 18.262 10.9401C17.82 10.9401 17.475 11.0391 17.18 11.1871C16.885 11.3361 16.689 11.5341 16.492 11.7321C16.311 11.9234 16.1912 12.1643 16.148 12.4241C16.098 12.6721 16.049 12.8691 16.049 13.0671H20.475C20.377 12.3251 20.131 11.8311 19.787 11.4841V11.4841ZM15.459 6.2901H20.967V7.6261H15.46V6.2901H15.459Z" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_48_1508">
                                        <rect width="24" height="24" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Footer Bottom -->
    </div>
</footer>

<!-- ===== Footer End ===== -->

<!-- ====== Back To Top Start ===== -->
<button class="ub ee ge jd nc ze sj wf zk f u v _" @click="window.scrollTo({top: 0, behavior: 'smooth'})"
        @scroll.window="scrollTop = (window.pageYOffset > 50) ? true : false" :class="{ 'rb' : scrollTop }">
    <svg class="ig bd fc" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
        <path
            d="M233.4 105.4c12.5-12.5 32.8-12.5 45.3 0l192 192c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L256 173.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l192-192z" />
    </svg>
</button>

<!-- ====== Back To Top End ===== -->
<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
<script defer src="js/bundle.js"></script>
<script defer
        src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015"
        integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ=="
        data-cf-beacon='{"version":"2024.11.0","token":"9a6015d415bb4773a0bff22543062d3b","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}'
        crossorigin="anonymous"></script>
</body>

</html>
