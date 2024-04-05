import os
import logging
import requests
import urllib.parse
from dotenv import load_dotenv
from langchain_openai import ChatOpenAI
from langchain_core.prompts import ChatPromptTemplate
from langchain_core.output_parsers import StrOutputParser
import sys

class MovieTMDB():
    def __init__(self):
        self.name = "movies_tmdb"
        self.description = "Interact with a Movie Expert AI to explore movie preferences."
        load_dotenv()
        API_KEY = os.getenv('OPEN_AI_KEY')
        self.llm = ChatOpenAI(openai_api_key=API_KEY)  # Initialize once and reuse

    def calculate_tokens(self, text):
        return len(text) + text.count(' ')

    def interact_with_ai(self, user_input, prompt_text):
        # Generate a more conversational and focused prompt
        prompt = ChatPromptTemplate.from_messages([("system", prompt_text)]+[("user", user_input)])
        output_parser = StrOutputParser()
        chain = prompt | self.llm | output_parser
        response = chain.invoke({"input": user_input})
        # Token usage logging and adjustment for more accurate counting
        tokens_used = self.calculate_tokens(prompt_text + user_input + response)
        logging.info(f"OpenAI API call made. Tokens used: {tokens_used}")
        return response, tokens_used

    def find_id_param(self, type, query):
        '''Takes type and query to find ids of movies, people and keywords'''
        try:
            query = urllib.parse.quote(query.encode('utf-8'))
            query_url = f'https://api.themoviedb.org/3/search/{type}?query={query}&include_adult=false&language=en-US&page=1'
            logging.info(f"response before encoding. {query_url}")
            result = self.callAPI(query_url)
            id = str(result['results'][0]['id'])
            return id
        except Exception as e:
            logging.error("Sorry, there was an error processing your request. Please try again.")
            logging.error(f"Error during interaction or no response: {e}")
            return None

    def get_genre_params(self, user_input):
        GENRE_PROMPT = os.getenv('GENRE_PROMPT')
        try:
            response, tokens_used = self.interact_with_ai(user_input, GENRE_PROMPT)
            logging.info(f"response for GENRE before encoding {response}")
            if response != 'none':
                param='with_genres='+urllib.parse.quote(response)
                return param, tokens_used
            else:
                logging.info(f"ai finds no genre. {response}")
                return None, tokens_used
        except Exception as e:
            logging.error(f"Error during interaction: {e}")
            return None,0

    def get_actor_params(self, user_input):
        ACTOR_PROMPT = os.getenv('ACTOR_PROMPT')
        try:
            query, tokens_used = self.interact_with_ai(user_input, ACTOR_PROMPT)
            logging.info(f"response before encoding. {query}")
            if query != 'none':
                actor_id=self.find_id_param('person',query)#returns first id in list or None
                if actor_id!=None:
                    param = 'with_people='+actor_id
                    return param, tokens_used
                else:
                    logging.info(f"ai finds no actor id for the query. {query}")
                    return None, tokens_used
            else:
                logging.info(f"ai finds no actor query. {query}")
                return None, tokens_used
        except Exception as e:
            logging.error(f"Error during interaction: {e}")
            return None,0

    def process_message(self, user_input):
        DISCOVER_URL = os.getenv('DISCOVER_URL')
        params = []
        total_tokens_used = 0

        #get genre param
        param, tokens_used = self.get_genre_params(user_input)
        if param!=None:
            params.append(param)
        total_tokens_used += tokens_used

        #get actor param
        param, tokens_used = self.get_actor_params(user_input)
        if param!=None:
            params.append(param)
        total_tokens_used += tokens_used
        
        params = '&'.join(params)
        url=DISCOVER_URL+params
        url = url.strip()
        return url, total_tokens_used

    def callAPI(self,url):
        BEARER_TOKEN = os.getenv('BEARER_TOKEN')
        headers = {
            "accept": "application/json",
            "Authorization": f"Bearer {BEARER_TOKEN}"
        }
        response = requests.get(url, headers=headers)
        logging.info(f"tmdb API call made.")
        return response.json()
    
    def execute(self, user_input):
        response, total_tokens_used = self.process_message(user_input)
        return response

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: python tmdb_chat.py \"input_text\"")
    else:
        input_text = sys.argv[1]
        movie_tmdb = MovieTMDB()
        myurl=movie_tmdb.execute(input_text)
        print(myurl)
        #print('https://api.themoviedb.org/3/discover/movie?include_adult=false&include_video=false&language=en-US&page=1&sort_by=popularity.desc&with_genres=28')