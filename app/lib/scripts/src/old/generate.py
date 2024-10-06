
import argparse
from generators import controller, model

def main():
    parser = argparse.ArgumentParser(description='Generate controllers and models.')
    subparsers = parser.add_subparsers(title='subcommands', dest='subcommand')

    controller_parser = subparsers.add_parser('controller', help='Generate a new controller')
    controller_parser.add_argument('name', help='Name of the controller')

    model_parser = subparsers.add_parser('model', help='Generate a new model')
    model_parser.add_argument('name', help='Name of the model')

    args = parser.parse_args()

    if args.subcommand == 'controller':
        controller.create_controller(args.name)
    elif args.subcommand == 'model':
        model.create_model(args.name)
    else:
        print("Invalid subcommand. Use 'controller' or 'model'.")

if __name__ == "__main__":
    main()