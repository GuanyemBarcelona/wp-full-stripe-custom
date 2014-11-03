<div class="wrap about-wrap">
    <h1><?php _e('Welcome to WP Full Stripe'); ?></h1>
    <div class="about-text">
        <p>Accept payments and subscriptions from your WordPress website. Created by
            <a href="http://mammothology.com">Mammothology</a></p>
    </div>
    <div class="changelog">
        <h3><?php _e('Help & Support'); ?></h3>
        <div class="feature-section images-stagger-right">
            <p>Check out our <a href="<?php echo admin_url("admin.php?page=fullstripe-help"); ?>">Help section</a> or visit the
                <a href="http://mammothology.com/forums/">support forums</a> if you have a question. You can also subscribe for premium support for FREE by
                <a href="http://eepurl.com/LBLHn">adding your email address to our mailing list.</a></p>
            <a class="button button-primary" href="http://eepurl.com/LBLHn">Subscribe for premium support</a>
            <h4><?php _e('Other Products'); ?></h4>
            <p>If you like WP Full Stripe you may be interested in some of our other products. We have something for everyone, including WordPress plugins, payment solutions, event booking software, drop-in forms and more.</p>
            <a class="button button-primary" href="http://codecanyon.net/collections/3301899-our-products?ref=mammothology">View all Mammothology products</a>
            <h4><?php _e('Changelog'); ?></h4>
            <p>Below is a list of the most recent plugin updates. We are committed to continually improving WP Full Stripe.</p>
            <div class="changelog-updates">
                <strong>September 6th 2014</strong>
                <blockquote>
                    <ul>
                        <li>Bugfix: Subscriptions create Stripe customer objects correctly again.</li>
                    </ul>
                 </blockquote>
                <strong>August 29th 2014</strong>
                <blockquote>
                    <ul>
                        <li>Stripe Customer objects are now created for charges, meaning better information about customers in your Stripe dashboard</li>
                        <li>Custom input has been moved from the description field to a charge metadata value</li>
                        <li>Fixed Stripe link on payments history tables</li>
                        <li>Stripe checkout forms now correctly save customer email</li>
                        <li>Locale strings for CAD accounts have been added</li>
                    </ul>
                </blockquote>
                <strong>July 23rd 2014</strong>
                <blockquote>
                    <ul>
                        <li>Hotfix to update transfers parameter due to Stripe API update</li>
                    </ul>
                </blockquote>
                <strong>July 20th 2014</strong>
                <blockquote>
                    <ul>
                        <li>Added option to use Stripe emails for payment receipts</li>
                        <li>Fixed issue with redirect ID field on edit forms</li>
                        <li>Added customer name to metatdata sent to Stripe on successful payment</li>
                    </ul>
                </blockquote>
                <strong>June 23rd 2014</strong>
                <blockquote>
                    <ul>
                        <li>New tabbed design on payment and subscription pages</li>
                        <li>New sortable table for subscriber list</li>
                        <li>Choose to show remember me option on checkout forms</li>
                        <li>Ability to choose custom image for checkout form</li>
                    </ul>
                </blockquote>
                <strong>June 21st 2014</strong>
                <blockquote>
                    <ul>
                        <li>You can now specify setup fees for subscriptions!</li>
                    </ul>
                </blockquote>
                <strong>June 18th 2014</strong>
                <blockquote>
                    <ul>
                        <li>Added ability to customize subscription form button text</li>
                        <li>Currency symbol now shows for plan summary price text</li>
                        <li>Some typos have been fixed & other UI improvements.</li>
                        <li>New About page.</li>
                    </ul>
                </blockquote>
                <strong>May 5th 2014</strong>
                <blockquote>
                    <ul>
                        <li>New system allows selection of different form styles</li>
                        <li>New 'compact' style for payment forms. More to come!</li>
                        <li>Tidy up of form code to allow easier creation of new form styles.</li>
                    </ul>
                </blockquote>
                <strong>Apr 20th 2014</strong>
                <blockquote>
                    <ul>
                        <li>Checkout form now uses currency set in the plugin options</li>
                        <li>Updated currency symbols throughout admin sections</li>
                        <li>Tested to work with latest release, WordPress 3.9</li>
                    </ul>
                </blockquote>
                <strong>Apr 19th 2014</strong>
                <blockquote>
                    <ul>
                        <li>Added address line 2 and state fields to billing address portion of forms</li>
                        <li>Used metadata parameter with Stripe API to better store customer email and address fields</li>
                        <li>Address fields on forms now take locale into account (Zip/Postcode, State/Region etc.)</li>
                        <li>Added new fields to customize email receipts</li>
                    </ul>
                </blockquote>
                <strong>Apr 13th 2014</strong>
                <blockquote>
                    <ul>
                        <li>New form type! Stripe Checkout forms are now supported. These are pre-styled, responsive forms.</li>
                        <li>You can now select to receive a copy of email receipts that are sent after successful payments.</li>
                        <li>More email validation added.</li>
                    </ul>
                </blockquote>
                <strong>Mar 21st 2014</strong>
                <blockquote>
                    <ul>
                        <li>You can now customize payment email receipts in the settings page</li>
                        <li>Stage 1 of major refactor of code, making it easier & faster to provide future updates.</li>
                        <li>Loads more action and filter hooks added to make plugin more extendable.</li>
                    </ul>
                </blockquote>
                <strong>Feb 17th 2014</strong>
                <blockquote>
                    <ul>
                        <li>Subscription forms now show total price at the bottom</li>
                        <li>Coupon codes can now be applied, showing total price to the customer</li>
                    </ul>
                </blockquote>
                <strong>Feb 15th 2014</strong>
                <blockquote>
                    <ul>
                        <li>Added option to send email receipts to your customers after successful payment</li>
                    </ul>
                </blockquote>
                <strong>Jan 26th 2014</strong>
                <blockquote>
                    <ul>
                        <li>Fixed an issue with copy/pasting Stripe API keys sometimes including extra spaces</li>
                    </ul>
                </blockquote>
                <strong>Jan 15th 2014</strong>
                <blockquote>
                    <ul>
                        <li>You can now edit your payment and subscription forms!</li>
                        <li>Improved table added for viewing payments which allows sorting by amount, date and more.</li>
                        <li>General code tidy up. More coming soon.</li>
                    </ul>
                </blockquote>
            </div>
        </div>
    </div>
</div>