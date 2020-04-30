import { shallow, mount } from '@vue/test-utils'
import expect from 'expect'
import BookComponent from '../../resources/js/components/BookComponent.vue'
import moxios from 'moxios'

describe ('BookComponent', () => {
    beforeEach(() => {
        moxios.install(axios);
    });

    afterEach(() => {
        moxios.uninstall(axios);
    });
    it ('says Books', () => {

        let wrapper = shallow(BookComponent);

        expect(wrapper.html()).toContain('Books');

    });
    it ('shows books fetched from api', (done) => {

        let wrapper = mount(BookComponent);

        moxios.stubRequest('api/book', {
            status: 200,
            response: [
                {
                    title: "Test",
                    author: "Test",
                }
            ],
        });

        moxios.wait(() => {
            expect(
                wrapper.html()).toContain('Test');
            done();
        });
    });
});
