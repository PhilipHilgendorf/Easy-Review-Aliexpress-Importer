import React, { useState, useEffect } from 'react';
import axios from 'axios';
import EraiSwitch from './ERAISwitch';
import EraiInput from './ERAIInput';
import EraiSelect from './ERAISelect';
import EraiSaveLoader from './ERAISaveLoader';

const ERAISettings = () => {
    const erai_settings = window.erai_settings;
    const [hideAnonymousBuyer, setHideAnonymousBuyer] = useState(erai_settings.hide_anonymous_buyer == '1');
    const [minimumStars, setMinimumStars] = useState(erai_settings.minimum_stars);
    const [reviewUrl, setReviewUrl] = useState(erai_settings.review_url);
    const [translate, setTranslate] = useState(erai_settings.translate == '1');
    const [ajaxUrl] = useState(erai_settings.ajax_url);
    const [deeplapikey, setDeeplapikey] = useState(erai_settings.deeplapikey);
    const [displayTranslate, setDisplayTranslate] = useState(translate ? '' : 'none');
    const [translateTo, setTranslateTo] = useState(erai_settings.translateTo);
    const [freeDeeplApi, setFreeDeeplApi] = useState(erai_settings.free_deepl_api =='1')
    const [loaderStatus, setLoaderStatus] = useState('');
    useEffect(() => {
        setDisplayTranslate(translate ? '' : 'none');
    }, [translate]);
    
    const handleSubmit = async (event) => {
        event.preventDefault();
        setLoaderStatus("loading");

        try {
            const formData = new FormData(event.target);
            await axios.post(ajaxUrl, formData, {
                headers: { 'X-WP-Nonce': window.erai_settings.nonce }
            });
            setLoaderStatus("success");
        } catch (error) {
            setLoaderStatus("error");
        }
    };

    var saveDataTimeout;
    const saveData = (e) => {
        clearTimeout(saveDataTimeout);
        saveDataTimeout = setTimeout((timeoutId) => {
            document.getElementById("erai-setting-form").dispatchEvent(new Event("submit", { cancelable: true, bubbles: true }));
        }, 2000, saveDataTimeout);
    };

    return (
        <div className="erai-page">
            <EraiSaveLoader status={loaderStatus} />
            <form action={ajaxUrl} method="POST" id="erai-setting-form" onChange={(e) => saveData(e)} onSubmit={(e) => handleSubmit(e)}>
                <div className="erai-page-container">
                    <section className="erai-table">
                        <div className="erai-tr">
                            <div className="erai-content-td">
                                <h3 className="text-lg">Hide AliExpress Buyer Identities</h3>
                                <p className="description-text">Enable this option to replace anonymous AliExpress usernames with randomly generated names.</p>
                            </div>
                            <div className="erai-controll-td">
                                <EraiSwitch
                                    name="hide_anonymous_buyer"
                                    defaultChecked={hideAnonymousBuyer}
                                    onChange={(e) => setHideAnonymousBuyer(e.target.checked)}
                                />
                            </div>
                        </div>
                        <div className="erai-tr">
                            <div className="erai-content-td">
                                <h3 className="text-lg">Minimum Stars Threshold</h3>
                                <p className="description-text">Automatically sets the review star rating to the selected minimum value if a review's rating falls below this threshold.</p>
                            </div>
                            <div className="erai-controll-td">
                                <EraiSelect
                                    name="minimum_stars"
                                    options={[{ label: "1 Star", value: "1" }, { label: "2 Star", value: "2" }, { label: "3 Star", value: "3" }, { label: "4 Star", value: "4" }, { label: "5 Star", value: "5" }]}
                                    value={minimumStars}
                                    onChange={(e) => setMinimumStars(e.target.value)}
                                />
                            </div>
                        </div>
                        <div className="erai-tr">
                            <div className="erai-content-td">
                                <h3 className="text-lg">Review URL</h3>
                                <p className="description-text">The AliExpress URL used to fetch reviews data for the product.</p>
                            </div>
                            <div className="erai-controll-td">
                                <EraiInput
                                    name="review_url"
                                    type="text"
                                    value={reviewUrl}
                                    onChange={(e) => setReviewUrl(e.target.value)}
                                />
                            </div>
                        </div>
                        <div className="erai-tr">
                            <div className="erai-content-td">
                                <h3 className="text-lg">Auto-Translate Reviews</h3>
                                <p className="description-text">Automatically translates reviews into your preferred language using DeepL by import.</p>
                            </div>
                            <div className="erai-controll-td">
                                <EraiSwitch
                                    name="translate"
                                    defaultChecked={translate}
                                    onChange={(e) => setTranslate(e.target.checked)}
                                />
                            </div>
                        </div>
                        <div className="erai-tr" style={{ display: displayTranslate }}>
                            <div className="erai-content-td">
                                <h3 className="text-lg">DeepL API Key</h3>
                                <p className="description-text">Enter your DeepL API key to enable high-quality translation capabilities.</p>
                            </div>
                            <div className="erai-controll-td">
                                <EraiInput
                                    name="deeplapikey"
                                    type="password"
                                    value={deeplapikey}
                                    onChange={(e) => setDeeplapikey(e.target.value)}
                                />
                            </div>
                        </div>
                        <div className="erai-tr" style={{ display: displayTranslate }}>
                            <div className="erai-content-td">
                                <h3 className="text-lg">Free DeepL API</h3>
                                <p className="description-text">Enable this option if your using the free API.</p>
                            </div>
                            <div className="erai-controll-td">
                                
                                <EraiSwitch
                                    name="freedeeplapi"
                                    defaultChecked={freeDeeplApi}
                                    onChange={(e) => setFreeDeeplApi(e.target.checked)}
                                />
                            </div>
                        </div>
                        <div className="erai-tr" style={{ display: displayTranslate }}>
                            <div className="erai-content-td">
                                <h3 className="text-lg">Translate to</h3>
                            </div>
                            <div className="erai-controll-td">
                                <EraiSelect
                                    name="translate_to"
                                    options={[{label: "Arabic", value:"AR"},{label: "Bulgarian", value:"BG"},{label: "Czech", value:"CS"},{label: "Danish", value:"DA"},{label: "German", value:"DE"},{label: "Greek", value:"EL"},{label: "English ", value:"EN"},{label: "English (British)", value:"EN-GB"},{label: "English (American)", value:"EN-US"},{label: "Spanish", value:"ES"},{label: "Estonian", value:"ET"},{label: "Finnish", value:"FI"},{label: "French", value:"FR"},{label: "Hungarian", value:"HU"},{label: "Indonesian", value:"ID"},{label: "Italian", value:"IT"},{label: "Japanese", value:"JA"},{label: "Korean", value:"KO"},{label: "Lithuanian", value:"LT"},{label: "Latvian", value:"LV"},{label: "Norwegian Bokmål", value:"NB"},{label: "Dutch", value:"NL"},{label: "Polish", value:"PL"},{label: "Portuguese", value:"PT"},{label: "Portuguese (Brazilian)", value:"PT-BR"},{label: "Portuguese", value:"PT"},{label: "Portuguese (excluding Brazilian Portuguese)", value:"PT-PT"},{label: "Romanian", value:"RO"},{label: "Russian", value:"RU"},{label: "Slovak", value:"SK"},{label: "Slovenian", value:"SL"},{label: "Swedish", value:"SV"},{label: "Turkish", value:"TR"},{label: "Ukrainian", value:"UK"},{label: "Chinese (simplified)", value:"ZH-HANS"},{label: "Chinese (traditional)", value:"ZH-HANT"}]}
                                    value={translateTo}
                                    onChange={(e) => setTranslateTo(e.target.value)}
                                />
                            </div>
                        </div>
                    </section>
                </div>
            </form>
        </div>
    );
};

export default ERAISettings;
